<?php

return array(
    /*全局*/
    'MODULE_ALLOW_LIST' => array('Admin', 'Api'),
    'LOAD_EXT_CONFIG' => 'connection_online,sdk,redis',
    'LOAD_EXT_FILE' => 'commons',
    'DEFAULT_MODULE' => 'Api', // 默认模块   
    'DEFAULT_CONTROLLER' => 'Index',
    'DEFAULT_ACTION' => 'Index',
    'URL_CASE_INSENSITIVE'  =>  true,   // 默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'             =>  2,       // URL访问模式,可选参数0、1、2、3,代表以下四种模式：   
    'DEFAULT_FILTER'        =>  'htmlspecialchars',
    'AUTOLOAD_NAMESPACE' => array(
        'Lib' =>BASE_PATH.'Lib',
    ),    
    
    /*Uid加密*/
    'DATA_CRYPT_TYPE'=>'Pmb',
    'DATA_CRYPT_KEY'=>'PMB@)!$CRYPT',
    'DATA_CRYPT_EXPRITE'=>'604800',//7天
    
    /*SESSION控制*/
    'SESSION_AUTO_START' => false,
    'VAR_SESSION_ID' => 'sid',
    'SESSION_OPTIONS' => array('use_cookies' => 0, 'exprite' => 3600),
    
    /*日志*/
    'LOG_RECORD' => true,
    'LOG_LEVEL' => 'EMERG,ALERT,CRIT,ERR,WARN,SQL,DEBUG',
    'LOG_TYPE' => 'File', // 日志记录类型 默认为文件方式
    
    /*多语言设置*/
    'DEFAULT_LANG' => 'zh-cn',
    'LANG_SWITCH_ON' => true,
    'LANG_AUTO_DETECT' => true,
    'LANG_LIST' => 'zh-cn,zh-tw,en',
    'VAR_LANGUAGE' => 'l',

    
    /*验证*/
    'AUTH_CONFIG' => array(
        'AUTH_ON' => true, //认证开关
        'AUTH_TYPE' => 1, // 认证方式，1为时时认证；2为登录认证。
    ),
    
    /* 数据缓存设置 */
    'DATA_CACHE_TIME'       =>  0,      // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS'   =>  false,   // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK'      =>  false,   // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX'     =>  '',     // 缓存前缀
    'DATA_CACHE_TYPE'       =>  'Redis',  // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_PATH'       =>  TEMP_PATH,// 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR'     =>  false,    // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL'       =>  1,        // 子目录缓存级别
    /**/
);
