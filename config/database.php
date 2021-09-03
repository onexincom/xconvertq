<?php

return [
    'discuzq' => [
        'driver'    => 'mysql',
        'host'      => 'localhost', //Q数据库地址
        'port' => '3306', //Q数据库端口
        'prefix'    => 'dzq_', //Q表前缀，没有则留空
        'database'  => 'dzq', //Q数据库名
        'username'  => 'root', //Q数据库用户
        'password'  => '', //Q数据库密码
        'charset'   => 'utf8mb4', //Q数据编码
        'collation' => 'utf8mb4_unicode_ci', //Q数据库字符集
    ],
    'discuzx' => [
        'driver'    => 'mysql',
        'host'      => 'localhost', //X数据库地址
        'port' => '3306', //X数据库端口
        'prefix'    => 'pre_', //X表前缀，没有则留空
        'database'  => 'dx3', //X数据库名
        'username'  => 'root', //X数据库用户
        'password'  => '', //X数据库密码
        'charset'   => 'utf8', //X数据编码
        'collation' => 'utf8_unicode_ci', //X数据库字符集
    ]
];
