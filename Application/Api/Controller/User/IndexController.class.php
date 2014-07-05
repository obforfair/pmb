<?php

namespace Api\Controller\User;

use Think\Controller;

class IndexController extends Controller {

    public function _initialize() {
        
    }

    public function index() {
        dd(222222);
    }

    public function show($name = null) {
        $m = M('User','','DB2');
        $m = $m->query("select * from user");
        dd($m);
    }

}