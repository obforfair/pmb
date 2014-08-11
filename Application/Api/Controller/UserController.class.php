<?php

/**
 * 用户相关接口
 */

namespace Api\Controller;

use Think\Controller;

class UserController extends Controller {

    /**
     * 获取注册码
     */
    public function getRegisterCode($mobile) {
        check_mobile($mobile) || jsonReturn(E_PARAERR, '手机号码格式错误');
        service('Tools', 'sendCode', $mobile);
        jsonReturn(E_SUCCESS);
    }

    /**
     * 注册手机用户
     */
    public function register($mobile, $pwd, $code) {

        I('request.mobile', '', 'request.check_mobile') ? $mobile = I('request.mobile') : jsonReturn(E_PARAERR, '手机号码格式错误');
        I('request.pwd', '', 'request.check_password') ? $pwd = I('request.pwd') : jsonReturn(E_PARAERR, '密码格式错误');
        I('request.code') ? $code = I('request.code') : jsonReturn(E_PARAERR);
        service('User', 'getFilterUser', array(array('mobile' => $mobile))) && jsonReturn(E_HAS_REGISTER);
//      service('Tools','checkCode',array($mobile,$code)) || jsonReturn(E_ERR_CODE);
        $r = service('User', 'registerUser', array(
            array('mobile' => $mobile, 'password' => $pwd, 'user_type' => 'mobile')));
        $r && ($sid = service('Tools', 'setSid', array($r, 'mobile')));
        isset($sid) ? jsonReturn(E_SUCCESS, array('sid' => $sid)) : jsonReturn(E_FAILURE);
    }

    /**
     * 用户登录
     */
    public function login($mobile, $pwd) {
        check_mobile($mobile) || jsonReturn(E_PARAERR, '手机号码格式错误');
        check_password($pwd) || jsonReturn(E_PARAERR, '密码格式错误');
//        I('request.mobile', '','check_mobile') ? $mobile = I('request.mobile') : jsonReturn(E_PARAERR, '手机号码格式错误');
//        I('request.pwd', '', 'check_password') ? $pwd = I('pwd') : jsonReturn(E_PARAERR, '密码格式错误');
        $where['mobile'] = $mobile;
        $user = M('User')->where($where)->field(array('user_id', 'password', 'status'))->find();
        $user || jsonReturn(E_NO_USER);
        if (md5($pwd) == $user['password']) {
            $sid = service('Tools', 'setSid', array($user['user_id'], 'mobile'));
            service('User', 'loginAction', array($user['user_id'], 'mobile', $sid));
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
        #判断是否是被允许平台
        if (!$plateform || empty($social_conf)) {
            jsonReturn(E_FAILURE, '该第三方平台不被允许');
        }
        #检测token是否正确
        $data = service('User', 'checkOauth', array($plateform, $open_id, $access_token));
        $data || jsonReturn(E_ERR_TOKEN);
        $oauthinfo = service('User', 'getOauthInfo', array($plateform, $open_id));
        #更新TOKEN
        if (!$oauthinfo) {
            $oauth_id = service('User', 'registerOauth', array(array('plateform' => $plateform, 'open_id' => $open_id, 'access_token' => $access_token)));
            $oauth_id ? $oauthinfo = array('oauth_id' => $oauth_id) : jsonReturn(E_FAILURE, '更新失败');
        } elseif ($oauthinfo['access_token'] != $access_token) {
            service('User', 'updateOauthInfo', array($oauthinfo['oauth_id'], array('access_token' => $access_token)));
        }
        #没有user_id 则注册新用户
        if (isset($oauthinfo['user_id'])) {
            $user_id = $oauthinfo['user_id'];
        } else {
            $user_id = service('User', 'oauthRegister', array($data, $plateform));
            service('User', 'updateOauthInfo', array($oauthinfo['oauth_id'], array('user_id' => $user_id)));
        }
        #如果绑定了用户则用主用户id
        $user_info = service('User', 'loadUserInfo', $user_id);
        $user_id = $user_info['bind_to'] ? $user_info['bind_to'] : $user_id;
        $user_id && $sid = service('Tools', 'setSid', array($user_id, $plateform));
        isset($sid) && service('User', 'loginAction', array($user_id, $plateform, $sid));
        isset($sid) ? jsonReturn(E_SUCCESS, array('sid' => $sid)) : jsonReturn(E_LOGIN_FAIL);
    }

    /**
     * 第三方用户绑定到手机用户
     * @param type $sid
     * @param type $mobile
     * @param type $code
     * @param type $pwd
     */
    public function bindTo($sid, $mobile, $code, $pwd) {
        check_mobile($mobile) || jsonReturn(E_FAILURE, '错误手机号码');
        check_password($pwd) || jsonReturn(E_FAILURE, '密码格式错误');
//        service('Toos')->checkcode($mobile,$code) || jsonReturn(E_ERR_CODE);
        # 解析用户信息 
        ($user = service('Tools', 'getSid')) || jsonReturn(E_FAILURE);
        $user_id = $user['uid'];
        $plateform = $user['plateform'];
        $user_info = service('User', 'loadUserInfo', $user_id);
        # 判断是否已经绑定 
        $user_type = service('User', 'switchUsertype', intval($user_info['user_type']));
        $user_type == 'mobile' && jsonReturn(E_FAILURE, '该账户已经绑定');
        # 判断手机用户是否已经注册 
        $has_registed = service('User', 'getFilterUser', array(array('mobile' => $mobile)));
        if ($has_registed) {//已经注册
            (md5($pwd) == $has_registed['password']) || jsonReturn(E_PWD_ERR);
            $bind_id = $has_registed['user_id'];
        } else {//没有注册则复制新手机用户
            $user_data = array('mobile' => $mobile, 'password' => $pwd);
            $bind_id = service('User', 'copyUser', array($user_id, array('mobile' => $mobile, 'password' => $pwd)));
        }
        #进行绑定
        $bind_id || jsonReturn(E_FAILURE, '绑定失败');
        $r = service('User', 'bind', array($user_id, $bind_id));
        $r && $sid = service('Tools', 'setSid', array($bind_id, 'mobile'));
        $r ? jsonReturn(E_SUCCESS, array('sid' => $sid)) : jsonReturn(E_FAILURE, '绑定失败');
    }

    /**
     * 获取用户信息
     * @param type $sid
     */
    public function getUserInfo() {
        ($user = service('Tools', 'getSid')) || jsonReturn(E_FAILURE);
        $userinfo = service('User', 'loadUserInfo', $user['uid']);
        $userinfo ? jsonReturn(E_SUCCESS, $userinfo) : jsonReturn(E_NULLGET);
    }

    /**
     * 更新用户信息
     */
    public function updateUserInfo() {
        ($user = service('Tools', 'getSid')) || jsonReturn(E_FAILURE);
        isset($_REQUEST['nickname']) && $data['nickname'] = I('nickname');
        isset($_REQUEST['email']) && $data['email'] = I('email', '', 'check_email');
        isset($_REQUEST['gender']) && $data['gender'] = I('gender', '', 'check_gender');
        isset($_REQUEST['introduction']) && $data['introduction'] = I('introduction', '', 'htmlspecialchars');
        $data || jsonReturn(E_FAILURE);
        foreach ($data as $item) {
            $item === FALSE && jsonReturn(E_PARAERR);
        }
        $r = service('User', 'updateUserInfo', array($user['uid'], $data));
        $r ? jsonReturn(E_SUCCESS) : jsonReturn(E_FAILURE);
    }

    /**
     * 更新头像
     * @param type $image_id
     */
    public function updateAvata($image_id) {
        ($user = service('Tools', 'getSid')) || jsonReturn(E_FAILURE);
        $r = service('User', 'updateUserInfo', array($user['uid'], array('avata' => $image_id)));
        $image_info = service('Tools', 'getimage', $image_id);
        $r ? jsonReturn(E_SUCCESS, $image_info) : jsonReturn(E_FAILURE);
    }

    public function getSid() {
        dd(service('Tools', 'getSid'));
    }

    public function setsid($uid, $plateform) {
        jsonReturn(E_SUCCESS, urlencode(service('Tools', 'setSid', array($uid, $plateform))));
    }

}
