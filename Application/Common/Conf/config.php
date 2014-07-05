<?php

return array(
    'CONTROLLER_LEVEL'          => 2,
    'MODULE_ALLOW_LIST'         => array('Home', 'Admin', 'Api'),
    'LOAD_EXT_CONFIG'           => 'user,db',
    'DEFAULT_MODULE'            => 'Api', // 默认模块   
    'DEFAULT_CONTROLLER'        => 'User/Index',
    'DEFAULT_ACTION'            => 'Index',
    'LOAD_EXT_CONFIG'           => 'user', 
    'DB_FIELDS_CACHE'           => false,
    'DB_DEPLOY_TYPE' => 1,
    'DB_RW_SEPARATE' => true,
    'DB' => array(
        'DB_TYPE' => 'pdo', // 数据库类型
        'DB_NAME' => 'paimaibao2,paimaibao2',
        'DB_PORT' => 3306,
        'DB_USER' => 'root', // 用户名
        'DB_PWD' => 'zhouwei', // 密码
        'DB_DSN' => 'mysql:host=localhost;dbname=paimaibao1;port=3306,mysql:host=localhsot;dbname=paimaibao2;port=3306',
        'DB_PREFIX' => '', // 数据库表前缀 
        'DB_CHARSET' => 'utf8mb4',
    ),
    'DB2'=>'mysql://root:zhouwei@localhost:3306/paimaibao2#utf8mb4',
    'redis' => array(
        "host" => "42.62.24.92",
        "port" => "7777",
        "password" => "85fsv#wgJmLgJQhyQfsyOJ11l1xiG3XZ"
    ),
);
