<?php

define('E_SUCCESS', 0x00000000); // 恭喜你！操作成功。
define('E_FAILURE', 0x00000001); // 对不起！操作失败。
define('E_SYSBUSY', 0x00000002); // 对不起！系统繁忙，请稍后再试。
define('E_UNLOGIN', 0x00000003); // 对不起！您需要登录后才能进行此操作。
define('E_NORIGHT', 0x00000004); // 对不起！您没有权限进行此操作。
define('E_DBERROR', 0x00000005); // 对不起！数据库繁忙，请稍后再试。
define('E_PARAERR', 0x00000006); // 对不起！操作失败，您的输入有错误。
define('E_NULLGET', 0x00000007); // 对不起！接口查询结果为空。
define('E_NULLGET', 0x00000007); // 对不起！接口查询结果为空。
define('E_EMPTY_MOBILE', 0x00001000); // 操作失败，手机号码不能为空。
define('E_ERR_PWD', 0x00001001); // 密码格式错误。
define('E_ERR_CODE', 0x00001002); // 验证失败
define('E_HAS_REGISTER', 0x00001003); // 改号码已经注册。
define('E_ERR_USERNAME', 0x00001004); // 用户名格式错误。
define('E_NO_USER', 0x00001005); // 用户名格式错误。
define('E_PWD_ERR', 0x00001006);

return array(
    "E00000000" => "操作成功", //E_SUCCESS
    "E00000001" => "操作失败", //E_FAILURE
    "E00000002" => "系统繁忙，请稍后再试", //E_SYSBUSY
    "E00000003" => "您需要登录后才能进行此操作", //E_UNLOGIN
    "E00000004" => "没有权限进行此操作", //E_NORIGHT
    "E00000005" => "数据库繁忙，请稍后再试", //E_DBERROR
    "E00000006" => "操作失败，参数有错误", //E_PARAERR
    "E00000007" => "接口查询结果为空", //E_NULLGET
    "E00001000" => "操作失败，手机号码不能为空", //E_EMPTY_MOBILE
    "E00001001" => "密码格式错误", //E_ERR_PWD
    "E00001002" => "验证失败", //E_ERR_CODE
    "E00001003" => "该号码已经注册", //E_HAS_REGISTER
    "E00001004" => "用户名格式错误", //E_ERR_USERNAME
    "E00001005" => "没有该用户", //E_ERR_USERNAME
    "E00001006" => "密码错误", //E_ERR_USERNAME
);
