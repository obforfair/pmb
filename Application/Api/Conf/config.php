<?php

define('SERVICE_PATH', APP_PATH . 'Api/Service/');
return array(
    /*公共*/
//    'ACTION_BIND_CLASS'=>true,   
    'SHOW_PAGE_TRACE' => true,
    'CONTROLLER_LEVEL' => 1,
    'ERROR_PAGE' => '/Public/error/api_error.html',
    'LOG_PATH' => '/var/log/www/lthink/',
    'DEFAULT_FILTER'        =>  'addslashes', 
    /*缓存*/
    'CACHE_REDIS'=>true,//是否使用redis
    'CACHE_MEMCACH'=>false,//是否使用memcache
    /*图片*/
    'IMAGE_SAVE_PATH'=>'/var/www/images/',
    'IMAGE_MAX_SIZE' => 3145728,
    'IMAGE_CROP_AVATA'=>'small:thumb:200_200_3,middle:thumb:400_400_3',
    'IMAGE_HOST'=>'http://image.pmb.com',
    
    /*上传*/
    'Upload'=>array(
        'mimes'         =>  array(), //允许上传的文件MiMe类型
        'maxSize'       =>  0, //上传的文件大小限制 (0-不做限制)
        'exts'          =>  array(), //允许上传的文件后缀
        'autoSub'       =>  true, //自动子目录保存文件
        'subName'       =>  array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
        'rootPath'      =>  '/var/www/images/', //保存根路径
        'savePath'      =>  '', //保存路径
        'saveName'      =>  array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
        'saveExt'       =>  '', //文件保存后缀，空则使用原后缀
        'replace'       =>  false, //存在同名是否覆盖
        'hash'          =>  true, //是否生成hash编码
        'callback'      =>  false, //检测文件是否存在回调，如果存在返回文件信息数组
        'driver'        =>  '', // 文件上传驱动
        'driverConfig'  =>  array(), // 上传驱动配置        
    ),
);
