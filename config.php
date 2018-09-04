<?php
return [
    'redis'=> [
        'scheme' =>'tcp',
        'host' => '127.0.0.1',
        'port' => 32768,
    ],
    'db' => [
        'host' => '127.0.0.1',
        'dbname' => 'blog',
        'user' => 'root',
        'pass' => ' ',
        'charset' => 'utf8'
    ],
    'email' => [
        'mode' => 'debug', // 调试模式 debug    生产模式 production 
        'port' => 25,
        'host' => 'smtp.126.com',
        'email' => 'czxy_qz@126.com',
        'code' => '12345678abcdefg'
    ]
];