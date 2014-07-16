<?php

namespace Api\Controller\Common;

use Think\Controller;

class IndexController extends Controller {

    public function _initialize() {
        
    }

    public function index() {
        dd(333333);
    }

    public function show($name = null) {
        $m = M('User');
        $m = $m->query("select * from user");
        dd($m);
    }

}