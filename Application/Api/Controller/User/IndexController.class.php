<?php

namespace Api\Controller\User;

use Think\Controller;
use Lib\Ob\Log;
use Lib\Ob\Auth;

class IndexController extends Controller {

    public function _initialize() {
        $auth = new Auth();
        $access_name =  strtolower( MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME );
     
        if(!$auth->check($access_name,1,array(1,2))){
            dd('error');
        }else{
            dd('success');
        }
    }

    public function index() {
        dd(222222);
    }

    public function show2() {
        service('User');
    }

}