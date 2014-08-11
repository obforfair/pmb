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

namespace Lib\Ob;

/**
 * 服务类
 */
class Service {

    public function __construct() {
        
    }

    public function getMCache($key, $params) {
        if (!$params) {
            $info = cache()->hgetall($key);
        } elseif (is_array($params)) {
            $info = cache()->hmget($key, $params);
        } else {
            $info = cache()->hget($key, $params);
        }
        
        $info && ($info = array_filter($info, function($var) {
            return (false !== $var);
        }));
        return $info;
    }

    public function upMCache($key, $data, $create = false) {
        $if_exists = cache()->exists($key);
        if ($create || $if_exists) {
            cache()->hmset($key, $data);
        }
    }

}
