<?php

namespace Api\Controller;

use Think\Controller;

class UserController extends Controller {

    /**
     * 获取注册码
     */
    public function getRegisterCode() {
        $mobile = I('mobile', '', 'check_mobile') ? I('mobile') : jsonReturn(E_PARAERR);
        service('Tools', 'sendCode', $mobile);
        jsonReturn(E_SUCCESS);
    }

    /**
     * 注册手机用户
     */
    public function register() {
        $mobile = I('mobile', '', 'check_mobile') ? I('mobile') : jsonReturn(E_PARAERR);
        $code = I('code') ? I('code') : jsonReturn(E_PARAERR);
        $pwd = I('pwd', '', 'check_password') ? I('pwd') : jsonReturn(E_ERR_PWD);
        service('User', 'ifHasUser', array(array('mobile' => $mobile))) && jsonReturn(E_HAS_REGISTER);
//      service('Tools','checkCode',array($mobile,$code)) || jsonReturn(E_ERR_CODE);
        $r = service('User', 'registerUser', array(
            array('mobile' => $mobile, 'password' => $pwd, 'user_type' => 'mobile')));
        jsonReturn($r ? E_SUCCESS : E_FAILURE);
    }

    /*
     * 用户登录
     */

    public function login($username, $pwd) {  
        if (check_mobile($username)) {
            $where['mobile'] = $username;
        } else {
            jsonReturn(E_ERR_USERNAME);
        }
        $user = M('User')->where($where)->field(array('user_id', 'password', 'status'))->find();
        empty($user) && jsonReturn(E_NO_USER);
        if (md5($pwd) == $user['password']) {
            M('User')->where(array('user_id' => $user['user_id']))->save(array('last_login' => date('Y-m-d H:i:s')));
            $sid = service('Tools', 'setUid', $user['user_id']);
            jsonReturn(E_SUCCESS, array('sid' => $sid));
        } else {
            jsonReturn(E_PWD_ERR);
        }
    }

    /**
     * 第三方平台登录
     * @param type $plateform
     * @param type $open_id
     * @param type $access_token
     */
    public function loginByOauth($plateform, $open_id, $access_token) {
        $social_conf = C('Think_SDK_' . strtoupper($plateform));
        //判断是否是被允许平台
        if (!$plateform || empty($social_conf)) {
            jsonReturn(E_FAILURE, '该第三方平台不被允许');
        }
        $oauthid = service('User', 'getOauthId', array($plateform, $open_id, $access_token));
        isset($oauthid['user_id']) && $sid = service('Tools', 'setUid', $oauthid['user_id']);
        $sid ? jsonReturn(E_SUCCESS, array('sid' => $sid)) : jsonReturn(E_LOGIN_FAIL);
    }

    /**
     * 手机注册用户绑定第三方登录
     */
    public function bind($plateform, $open_id, $access_token, $sid) {       
        $user_id = service('Tools', 'getUid');
        $user_id || jsonReturn(E_FAILURE);
        $bind_id = service('User', 'getOauthId', array($plateform, $open_id, $access_token));
        $bind_id || jsonReturn(E_ERR_TOOKEN);
        $oauth_info = M('user_oauth_token')->where(array('oauth_id' => $bind_id['oauth_id']))->find();
        $oauth_info['bind_to'] && jsonReturn(E_HAS_BIND);
        $r = M('user_oauth_token')->where(array('oauth_id' => $bind_id['oauth_id']))->save(array('bind_to' => $user_id));
        return $r ? jsonReturn(E_SUCCESS) : jsonReturn(E_FAILURE);
    }

    /**
     * 解除绑定
     * @param type $sid
     * @param type $plateform
     */
    public function unbind($sid, $plateform) {
        //TODO::查看是否已经绑定
        $user_id = service('Tools', 'getUid');
        $user_id || jsonReturn(E_FAILURE);
        $r = M('user_oauth_token')->where(array('bind_to' => $user_id, 'plateform' => $plateform))
                ->save(array('bind_to' => ''));
        $r ? jsonReturn(E_SUCCESS) : jsonReturn(E_FAILURE);
    }

    /**
     * 获取用户信息
     * @param type $sid
     */
    public function getUserInfo($sid) {
        $user_id = service('Tools', 'getUid');
        $user_id || jsonReturn(E_FAILURE);
        $userinfo = service('User', 'getUserInfo', $user_id);
        $userinfo ? jsonReturn(E_SUCCESS, $userinfo) : jsonReturn(E_FAILURE);
    }

    /**
     * 更新用户信息
     */
    public function updateUserInfo() {
        $user_id = service('Tools', 'getUid');
        $user_id || jsonReturn(E_FAILURE);        
        isset($_REQUEST['nickname']) && $data['nickname'] = I('nickname');
        isset($_REQUEST['email']) && $data['email'] = I('email', '', 'check_email');
        isset($_REQUEST['gender']) && $data['gender'] = I('gender', '', 'check_gender');
        isset($_REQUEST['introduction']) && $data['introduction'] = I('introduction','','htmlspecialchars');
        $data || jsonReturn(E_FAILURE);
        foreach ($data as $item) {
            $item === FALSE && jsonReturn(E_PARAERR);
        }
        $r = service('User','updateUserInfo',array($user_id,$data));
        $r ? jsonReturn(E_SUCCESS) : jsonReturn(E_FAILURE);
    }

    public function getUid() {
        dd(service('Tools', 'getUid'));
    }

    public function setsid($uid) {
        jsonReturn(E_SUCCESS, urlencode(service('Tools', 'setUid', $uid)));
    }

}
