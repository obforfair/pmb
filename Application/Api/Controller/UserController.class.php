<?php

namespace Api\Controller;

use Think\Controller;

class UserController extends Controller {

    /**
     * 获取验证码
     */
    public function getRegisterCode() {
        $mobile = I('mobile', '', 'check_mobile') ? I('mobile'): jsonReturn(E_PARAERR);
        service('Tools', 'sendCode', $mobile);
        jsonReturn(E_SUCCESS);
    }

    /**
     * 注册手机用户
     */
    public function register() {
        $mobile = I('mobile', '', 'check_mobile') ? I('mobile'): jsonReturn(E_PARAERR);
        $code = I('code') ? I('code') : jsonReturn(E_PARAERR);
        $pwd = I('pwd','','check_password') ? I('pwd') : jsonReturn(E_ERR_PWD);
       
        service('User','ifHasUser',array(array('mobile'=>$mobile))) && jsonReturn(E_HAS_REGISTER);
//      service('Tools','checkCode',array($mobile,$code)) || jsonReturn(E_ERR_CODE);
        $r = service('User','registerUser',array(array('mobile'=>$mobile,'password'=>$pwd)));
        jsonReturn($r ? E_SUCCESS : E_FAILURE);
    }
    
    public function login(){
        $username = I('username') ? I('username') : '';
        $pwd = I('pwd') ? md5(I('pwd')) : jsonReturn(E_PARAERR);
        
        if(check_mobile($username)){
            $where['mobile']=$username;
        }else if (check_email($username)) {
            $where['email']=$username;
        }else{
            jsonReturn(E_ERR_USERNAME);
        }
        $user = M('User')->where($where)->field(array('password','status'))->find();
        empty($user) && jsonReturn(E_NO_USER);
        if(md5($pwd) === $user['password']){
            jsonReturn(E_SUCCESS);
        }else{
            jsonReturn(E_PWD_ERR);
        }
    }
}
