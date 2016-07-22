<?php
namespace Library;

use Model;
use Mustache_Engine, Mustache_Loader_FilesystemLoader;
use Library\Date;
use Library\User;

class Mailer
{
    protected static $config;
    protected static $doctrine;
    protected static $mailer;
    protected static $template;

    public static function initialize()
    {
        global $app;

        if ( ! static::$config) {
            static::$config = $app->getContainer()->get('config');
        }

        if ( ! static::$mailer) {
            static::$mailer = $app->getContainer()->get('mailer');
        }

        if ( ! static::$doctrine) {
            static::$doctrine = $app->getContainer()->get('doctrine');
        }

        if ( ! static::$template) {
            static::$template = new Mustache_Engine([
                'loader' => new Mustache_Loader_FilesystemLoader(BASEPATH . 'templates/email', [
                    'extension' => '.html'
                ]),
                'partials' => [
                    'logo' => static::getLogo()
                ]
            ]);
        }
    }

    public static function getLogo()
    {
        // static::initialize();

        $url = static::$config['base_url'].'assets/img/logo_coca-cola.png';

        $logo = sprintf("<a href=\"%s\"><img src=\"%s\"></a><br />", static::$config['base_url'], $url);

        return $logo;
    }

    public static function sendForgotPassword(Model\User $user, Array $data = [])
    {
        static::initialize();

        $data = [
            'url' => static::$config['front_url_prefix'] . 'auth/reset-password/' . $data['token']
        ];

        $subject = static::$template->render('forgot-password/title', $data);
        $body = static::$template->render('forgot-password/content', $data);

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
        static::$mailer->send();
    }

    public static function sendRequestConfirm(Model\Request $request)
    {
        static::initialize();

        $data = [
            'code' => str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'url' => static::$config['app_url_prefix'] . 'request/' . $request->id
        ];

        $subject = static::$template->render('request-confirm/title', $data);
        $body = static::$template->render('request-confirm/content', $data);
        $user = $request->creator;

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
        static::$mailer->send();
    }

    public static function sendApprovalPending(Model\User $approver, Model\Request $request, Model\User $prevApprover = null)
    {
        static::initialize();

        if ($prevApprover) {
            $prevApprover->department = $prevApprover->getApproverDepartmentString();
        }

        $data = [
            'code' => str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'url' => static::$config['app_url_prefix'] . 'request/' . $request->id,
            'contact' => static::$config['app_url_prefix'] . 'contact',
            'approver' => $prevApprover
        ];

        $subject = static::$template->render('approval-pending/title', $data);
        $body = static::$template->render('approval-pending/content', $data);

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($approver->email, $approver->firstName.' '.$approver->lastName);
        static::$mailer->send();
    }

    public static function sendApprovedOnStage(Model\Request $request)
    {
        static::initialize();

        $delivery = $request->getWantedDelivery();
        $delivery['location'] = current($delivery['locations']);
        $delivery['datetime'] = Date::format($delivery['time']);

        $data = [
            'code' => str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'url' => static::$config['app_url_prefix'] . 'request/' . $request->id,
            'contact' => static::$config['app_url_prefix'] . 'contact',
            'stage' => $request->getStageProgressString(),
            'approver' => $request->getRecentApprover(),
            'completed' => ($request->status == 3),
            'delivery' => $delivery,
            'toAdmin' => false
        ];

        $subject = static::$template->render('approved-stage/title', $data);
        $body = static::$template->render('approved-stage/content', $data);
        $user = $request->creator;

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
        static::$mailer->send();

        // --------------------------------------------------------------------
        // also send to shipping if it already completed

        if ($data['completed']) {
            $subject = static::$template->render('shipping-pending/title', $data);
            $body = static::$template->render('shipping-pending/content', $data);

            static::$mailer->Subject = $subject;
            static::$mailer->Body = $body;

            $shippingGroupId = $request->isSouthern ? 2 : 1;
            $users = User::getShipping($shippingGroupId);

            foreach ($users as $user) {
                static::$mailer->ClearAddresses();
                static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
                static::$mailer->send();
            }
        }

        // --------------------------------------------------------------------
        // also to all admin

        // set admin flag
        $data['toAdmin'] = true;

        $subject = static::$template->render('approved-stage/title', $data);
        $body = static::$template->render('approved-stage/content', $data);

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;

        $users = User::getAdmin();

        foreach ($users as $user) {
            static::$mailer->ClearAddresses();
            static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
            static::$mailer->send();
        }
    }


    public static function sendRequestNotApprove(Model\Request $request)
    {
        static::initialize();

        $data = [
            'code' => str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'url' => static::$config['app_url_prefix'] . 'request/create'
        ];

        $subject = static::$template->render('request-not-approved/title', $data);
        $body = static::$template->render('request-not-approved/content', $data);
        $user = $request->creator;

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
        static::$mailer->send();

        // --------------------------------------------------------------------
        // also to all admin

        // set admin flag
        $data['toAdmin'] = true;

        $subject = static::$template->render('request-not-approved/title', $data);
        $body = static::$template->render('request-not-approved/content', $data);

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;

        $users = User::getAdmin();

        foreach ($users as $user) {
            static::$mailer->ClearAddresses();
            static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
            static::$mailer->send();
        }
    }

    public static function sendRequestShipped(Model\Request $request)
    {
        static::initialize();

        $delivery = $request->getShippings(true);

        // to use wantedDelivery or shippings
        if (isset($delivery['locations'])) {
            $delivery['location'] = current($delivery['locations']);
        } else {
            $delivery = current($delivery);
            $delivery['location'] = $delivery;
        }

        $delivery['datetime'] = Date::format($delivery['time']);

        $data = [
            'code' => str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'url' => static::$config['app_url_prefix'] . 'request/' . $request->id,
            'delivery' => $delivery
        ];

        $subject = static::$template->render('request-shipped/title', $data);
        $body = static::$template->render('request-shipped/content', $data);
        $user = $request->creator;

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
        static::$mailer->send();

        // --------------------------------------------------------------------

        $users = User::getAdmin();

        foreach ($users as $user) {
            static::$mailer->ClearAddresses();
            static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
            static::$mailer->send();
        }
    }

    public static function sendDeliveryAlert(Model\Request $request)
    {
        static::initialize();

        $data = [
            'code' => str_pad($request->id, 6, '0', STR_PAD_LEFT),
            'url' => static::$config['app_url_prefix'] . 'request/' . $request->id
        ];

        $subject = static::$template->render('delivery-alert/title', $data);
        $body = static::$template->render('delivery-alert/content', $data);
        $user = $request->creator;

        static::$mailer->Subject = $subject;
        static::$mailer->Body = $body;
        static::$mailer->ClearAddresses();
        static::$mailer->addAddress($user->email, $user->firstName.' '.$user->lastName);
        static::$mailer->send();
    }
}
