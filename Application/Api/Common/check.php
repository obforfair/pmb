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

function check_time($time){
     if (preg_match("/^\d{4}-\d{2}-\d{2}(\s\d{2}:\d{2}:\d{2})?$/i", $time)) {
        return $time;
    } else {
        return false;
    }       
}
/**
 * 检测性别
 * @param type $gender
 * @return type
 */
function check_gender($gender){
    switch($gender){
        case '1':
        case '男':
            return 1;
            break;
        case '2':
        case '女':
            return 2;
            break;
        default:
            return 0;
    }
}

function inverse_gender($gender){
    switch($gender){
        case '1':
            return '男';
            break;
        case '2':
            return '女';
            break;
        default:
            return '未知';
    }    
}

