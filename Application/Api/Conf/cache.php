<?php

/* 缓存 */
return array(
    /* 数据缓存设置 */
    'DATA_CACHE_TIME' => 0, // 数据缓存有效期 0表示永久缓存
    'DATA_CACHE_COMPRESS' => false, // 数据缓存是否压缩缓存
    'DATA_CACHE_CHECK' => false, // 数据缓存是否校验缓存
    'DATA_CACHE_PREFIX' => '', // 缓存前缀
    'DATA_CACHE_TYPE' => 'REDIS', // 数据缓存类型,支持:File|Db|Apc|Memcache|Shmop|Sqlite|Xcache|Apachenote|Eaccelerator
    'DATA_CACHE_DEFAULT'=>'EMPTYS',
    'DATA_CACHE_PATH' => TEMP_PATH, // 缓存路径设置 (仅对File方式缓存有效)
    'DATA_CACHE_SUBDIR' => false, // 使用子目录缓存 (自动根据缓存标识的哈希创建子目录)
    'DATA_PATH_LEVEL' => 1, // 子目录缓存级别    
    /*前缀*/
    'PREFIX_USER' => 'USER_INFO:',
    'EXPIRES_USER' => 86400, //24小时
    'PREFIX_AUTION' => 'AUTION_INFO:',
    'EXPIRES_AUTION' => 86400, //24小时
    'PREFIX_AUTION_LIST_ID'=>'AUTION_IDS:',
    'EXPIRES_AUTION_LIST_ID' => 1800, //24小时
    'PREFIX_IMAGE' => 'IMAGE_PMB:',
    'EXPIRES_IMAGE'=>86400 //24小时
);

