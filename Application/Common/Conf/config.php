<?php

return array(
    'MODULE_ALLOW_LIST' => array('Admin', 'Api'),
    'LOAD_EXT_CONFIG' => 'connection_online',
    'LOAD_EXT_FILE' => 'commons',
    'DEFAULT_MODULE' => 'Api', // 默认模块   
    'DEFAULT_CONTROLLER' => 'Common/Index',
    'DEFAULT_ACTION' => 'Index',
    'VIEW_PATH' => './Theme/',
    'SESSION_AUTO_START' => TRUE,
    'VAR_SESSION_ID' => 'sid',
    'SESSION_OPTIONS' => array('use_cookies' => 1, 'exprite' => 3600),
    'LOG_RECORD' => true,
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN,SQL,DEBUG',
    'LOG_TYPE' => 'File', // 日志记录类型 默认为文件方式
    'AUTOLOAD_NAMESPACE' => array(
        'Lib' => 'Lib',
    ),
    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
    )
);
