<?php
define('URL_CALLBACK', 'http://video.test.spriteapp.com/user/get_token?type=');
return array(
    'THINK_SDK_TYPES'=>array('qq','sina','tencent'),
    'THINK_SDK_QQ' => array(
//        'APP_KEY' => '100584307',
        'APP_KEY'=> '100330589',
        'APP_SECRET' => '3c2a54df071876af3f1a5a6d7d0ac6dd',
        'CALLBACK' => URL_CALLBACK . 'qq',
        'API'=>array(
            'userinfo'=>'user/get_user_info',
        )
    ),
    'THINK_SDK_TENCENT' => array(
        'APP_KEY' => '801463297',
        'APP_SECRET' => '374d43e7db98005d84f71f6cfde31dcf',
        'CALLBACK' => URL_CALLBACK . 'tencent',
        'API'=>array(
            'userinfo'=>'user/info',
        )
    ),   
    'THINK_SDK_SINA' => array(
        'APP_KEY' => '3899758802',
        'APP_SECRET' => '9a685a37f45eb8a082d3177ff994cd6b',
        'CALLBACK' => URL_CALLBACK . 'sina',
        'API'=>array(
            'userinfo'=>'users/show',
        )        
    ),
    'THINK_SDK_WEIXIN' => array(
        'APP_KEY' => 'wx074e7567d3f4a052',
        'APP_SECRET' => '855115c5e6dd7c4e078491d2e11bf175',
        'CALLBACK' => URL_CALLBACK . 'sina',
        'API'=>array(
            'userinfo'=>'/sns/userinfo',
        )        
    ),    
);