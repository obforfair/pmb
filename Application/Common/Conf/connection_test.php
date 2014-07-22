<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
return array(
    #DB
    'DB_FIELDS_CACHE' => true,
    'DB_DEPLOY_TYPE' => 1,
    'DB_RW_SEPARATE' => true,
    'DB_TYPE' => 'mysqli',
    'DB_HOST' => '192.168.2.30,192.168.2.30',
    'DB_NAME' => 'pmb1,pmb1',
    'DB_PORT' => 3306,
    'DB_PREFIX' => 'bmp_',
    'DB_USER' => 'admin', // 用户名
    'DB_PWD' => 'zhouwei', // 密码
    'DB_CHARSET' => 'utf8mb4',
    'DB2' => 'mysql://root:zhouwei@localhost:3306/paimaibao2#utf8mb4',
    #REDIS
    "REDIS_HOST" => "127.0.0.1",
    "REDIS_PORT" => "6379",
        #MEMCACHE
);

