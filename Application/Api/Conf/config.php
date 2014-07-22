<?php

define('SERVICE_PATH', APP_PATH . 'Api/Service/');
return array(
//    'ACTION_BIND_CLASS'=>true,   
    'SHOW_PAGE_TRACE' => true,
    'CONTROLLER_LEVEL' => 1,
    'ERROR_PAGE' => '/Public/error/api_error.html',
    'LOG_PATH' => '/var/log/www/lthink/',
    'URL_ROUTER_ON' => false,
    'URL_ROUTE_RULES' => array(
        'index/show/:id' => array('index/show'),
    ),
    'URL_MAP_RULES' => array(
        'new/top' => 'news/index/type/top'
    ),
);
