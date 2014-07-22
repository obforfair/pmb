<?php

/**
 * 工具
 */

namespace Api\Service;

use Lib\Ob\Service;

class ToolsService extends Service {

    public function __construct() {
        
    }

    /**
     * 发送手机消息
     * @param type $mobile
     * @param type $message
     */
    public function sendMessage($mobile, $content) {
        $data = array(
            'userid' => 'mxzd',
            'pwd' => 'mxzd123',
            'mobiles' => $mobile,
            'content' => $content . '【珠宝网】', // iconv('utf-8', 'gbk', $content. '-【珠宝网】'),
            'subcode' => '3007'
        );
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_URL => 'http://ess.mobase.cn/clientapi/sendsms',
            CURLOPT_POSTFIELDS => http_build_query($data),
            //  CURLOPT_HTTPHEADER      => array("charset=utf-8"),
            CURLOPT_RETURNTRANSFER => true
        ));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    /**
     * 发送手机验证码
     * @param type $mobile
     */
    public function sendCode($mobile) {
        $register_code = rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9) . rand(0, 9);
        $content = "您的拍卖宝验证码为：" . $register_code . " ，为了您的账号安全，请勿将验证码转发给其他人。";
        if (!cache('PMB_MOBILE_CHECK_FILTER:' . $mobile)) {
            $this->sendMessage($mobile, $content);
            //每60秒可以发送一次，
            cache('PMB_MOBILE_CHECK_FILTER:' . $mobile, $register_code, 60);
            //code保存时间为600秒
            cache('PMB_MOBILE_CHECK:' . $mobile, $register_code, 600);
        }
    }

    /**
     * 检测手机验证码
     * @param type $mobile
     * @param type $code
     * @return boolean
     */
    public function checkCode($mobile, $code) {
        if($code == cache('PMB_MOBILE_CHECK:' . $mobile)){
            return true;
        }else{
            return false;
        }
    }

}

?>
