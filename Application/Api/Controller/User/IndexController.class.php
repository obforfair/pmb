<?php

namespace Api\Controller\User;

use Think\Controller;
use Lib\Ob\Log;
use Lib\Ob\Role;

class IndexController extends Controller {

    public function _initialize() {
        $auth = new Role();
        $auth->check('a1', 1); 
    }

    public function index() {
        dd(222222);
    }

    public function show2() {
        dd(session_id());
    }

}