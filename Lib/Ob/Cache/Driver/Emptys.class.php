<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Lib\Ob\Cache\Driver;

use Lib\Ob\Cache;

defined('THINK_PATH') or exit();

class Emptys {

    public function connect() {
        return true;
    }

    public function __call($name,$arg) {
        return false;
    }
    
    public function __callStatic($name, $arguments) {
        return false;
    }
    
    public function __set($name, $value) {
        return false;
    }

}
