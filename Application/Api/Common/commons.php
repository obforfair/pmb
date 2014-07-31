<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 验证手机号是否合法
 * @param type $mobile
 * @return boolean
 */
function check_mobile($mobile) {
    if (preg_match("/^\d{11}$/", $mobile)) {
        return $mobile;
    } else {
        return false;
    }
}

/**
 * 验证密码格式是否合法
 * @param type $password
 * @return boolean
 */
function check_password($password) {
    if (preg_match("/^[0-9a-zA-Z\!\@\#\$\%\^\&\*\(\)_]{6,20}$/", $password)) {
        return $password;
    } else {
        return false;
    }
}

/**
 * 检测email是否合法
 */
function check_email($email){
     if (preg_match("/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/i", $email)) {
        return $email;
    } else {
        return false;
    }   
    
}

function check_gender($gender){
    $gernder = intval($gender);
    return in_array($gernder, array(0,1,2)) ? $gender : 0;
    
}
?>
