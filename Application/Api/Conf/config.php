<?php

return array(
//    'ACTION_BIND_CLASS'=>true,   
    'CONTROLLER_LEVEL'  =>2,
    'URL_ROUTER_ON' => false,
    'URL_ROUTE_RULES' => array(
        'index/show/:id' => array('index/show'),
    ),
    'URL_MAP_RULES' => array(
        'new/top' => 'news/index/type/top'
    ),
);