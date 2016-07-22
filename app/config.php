<?php

$containerIp = gethostbyname(gethostname());

return [
    'config' => [
        'doctrine' => [
            'connection' => [
                'driver'   => 'pdo_mysql',
                'host'     => 'mysql',
                'dbname'   => 'cocacola',
                'user'     => 'cocacola',
                'password' => 'cocacola',
                'charset'  => 'utf8'
            ],
            'annotation_paths' => [
                BASEPATH.'models'
            ],
            'annotation_cache_dir' => BASEPATH.'cache'
        ],
        'mail' => [
            'hostname'   => 'mail',
            'username'   => '',
            'password'   => '',
            'port'       => '1025',
            'auth'       => false,
            'from_email' => 'NamthipGivesBack@coca-cola.co.th',
            'from_name'  => 'โครงการน้ำทิพย์ปันน้ำใจให้กาชาด'
        ],
        'base_url' => 'http://'.$containerIp.':3000/',
        'front_url_prefix' => 'http://'.$containerIp.':3000/#!/',
        'app_url_prefix' => 'http://'.$containerIp.':3000/app/#!/',
        'time_to_approve' => 30, // time to standby for approve in each level, in seconds
        'incomplete_notification_unit' => 'day'
    ],
    'settings' => [
        'displayErrorDetails' => true
    ],
    // you have to override this value in config.local.php
    'jwtKey' => '$t.~>?S>2]27?4g'
];
