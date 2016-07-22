<?php


return [
    'doctrine' => function ($container) {
        return Library\DoctrineCouchDBConnector::create($container);
    },
    'mailer' => function ($container) {
        $config = $container->get('config');

        $mail = new PHPMailer;

        $mail->Host     = $config['mail']['hostname'];
        $mail->Username = $config['mail']['username'];
        $mail->Password = $config['mail']['password'];
        $mail->Port     = $config['mail']['port']; // 1025;
        $mail->SMTPAuth = $config['mail']['auth']; // true;
        $mail->CharSet = "utf-8";

        $mail->isSMTP();
        $mail->setFrom($config['mail']['from_email'], $config['mail']['from_name']);
        $mail->isHTML(true);

        return $mail;
    },
    // 'errorHandler' => function ($container) {
    //     return new Library\Handler\Error($container['logger']);
    // },
    'notFoundHandler' => function ($container) {
        return function ($request, $response) use ($container) {
            return $response
                ->withStatus(404)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => 'Resource not found.']));
        };
    },
    'badRequestHandler' => function ($container) {
        return function($request, $response, $instruction = "Bad request.") use ($container) {
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode(['error' => $instruction]));
        };
    },
    'jsonHandler' => function ($container) {
        return function ($request, $response, $data) use ($container) {
            return $response
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($data));
        };
    }
];
