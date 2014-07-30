<?php

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Lib\Ob\Crypt\Driver;

/**
 * Base64 加密实现类
 */
class Pmb {

    /**
     * 加密字符串
     * @param string $str 字符串
     * @param string $key 加密key
     * @param integer $expire 有效期（秒）     
     * @return string
     */
    public static function encrypt($data, $key, $expire = 0) {
        $expire = sprintf('%010d', $expire ? $expire + time():0);
        $key  = md5($key); 
        $data = $expire.$data;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $data, MCRYPT_MODE_ECB, $iv);
        return base64_encode($crypttext);
    }

    /**
     * 解密字符串
     * @param string $str 字符串
     * @param string $key 加密key
     * @return string
     */
    public static function decrypt($data, $key) {
         $key    = md5($key);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $data = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($data), MCRYPT_MODE_ECB, $iv);
        $expire = substr($data,0,10);
        if($expire > 0 && $expire < time()) {
            return '';
        }
       return substr($data,10);
    }

}
