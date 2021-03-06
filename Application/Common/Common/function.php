<?php

/**
 * 实例化service
 * @param string $class
 * @param string $baseUrl
 * @param type $ext
 * @return Service 
 */
use Lib\Ob\Cache;

/**
 * 注册服务类
 * @param string $class
 * @param type $func
 * @param type $args
 * @param string $baseUrl
 * @param type $ext
 * @return type
 */
function service($class, $func, $args = array(), $baseUrl = '', $ext = '.class.php') {
    $args = (array) $args;
    if (empty($baseUrl))
        $baseUrl = SERVICE_PATH;
    $class = $class . 'Service';
    $class_namespace = 'Api\\Service\\' . $class;
    import($class, $baseUrl, $ext);
    $service = class_exists($class) ? new $class : new $class_namespace;
    if ($func) {
        return call_user_func_array(array($service, $func), $args);
    } else {
        return $service;
    }
}

/**
 * ajax返回数据结果
 * @param type $data
 * @param type $type
 */
function ajaxReturn($data, $type = '') {
    if (empty($type))
        $type = C('DEFAULT_AJAX_RETURN');
    switch (strtoupper($type)) {
        case 'JSON' :
// 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            exit(json_encode($data));
        case 'XML' :
// 返回xml格式数据
            header('Content-Type:text/xml; charset=utf-8');
            exit(xml_encode($data));
        case 'JSONP':
// 返回JSON数据格式到客户端 包含状态信息
            header('Content-Type:application/json; charset=utf-8');
            $handler = isset($_GET[C('VAR_JSONP_HANDLER')]) ? $_GET[C('VAR_JSONP_HANDLER')] : C('DEFAULT_JSONP_HANDLER');
            exit($handler . '(' . json_encode($data) . ');');
        case 'EVAL' :
// 返回可执行的js脚本
            header('Content-Type:text/html; charset=utf-8');
            exit($data);
    }
}

/**
 * errorMessage
 * 获取和设置错误信息
 */
function EM($name = null, $value = null, $default = null) {
    static $_errinfo = array();

// 无参数时获取所有
    if (empty($name)) {
        return $_errinfo;
    }
// 优先执行设置获取或赋值
    if (is_string($name)) {
        if (is_null($value)) {
            return isset($_errinfo[$name]) ? $_errinfo[$name] : $default;
        }
    }
// 批量设置
    if (is_array($name)) {
        $_errinfo = array_merge($_errinfo, array_change_key_case($name, CASE_UPPER));
        return;
    }
    return null; // 避免非法参数
}

/*
 * 统一返回数据格式
 */

function jsonReturn($ecode = E_FAILER, $data = array()) {
    if (is_int($ecode)) {
        $ecode = sprintf('E%08X', $ecode);
    }

    $return['info']['ecode'] = $ecode;
    $return['info']['msg'] = EM($ecode);

    $data && $return['info']['data'] = (array) $data;
    ajaxReturn($return, 'json');
}

/**
 * 缓存
 * @staticvar string $cache
 * @param type $name
 * @param type $value
 * @param type $options
 * @return type
 */
function cache($name = null, $value = '', $options = null) {
    static $cache = '';
    if (is_array($options) && empty($cache)) {
        // 缓存操作的同时初始化
        $type = isset($options['type']) ? $options['type'] : '';
        $cache = Cache::getInstance($type, $options);
    } elseif (is_array($name)) { // 缓存初始化
        $type = isset($name['type']) ? $name['type'] : '';
        $cache = Cache::getInstance($type, $name);
        return $cache;
    } elseif (empty($cache)) { // 自动初始化
        $cache = Cache::getInstance();
    }
    if (null === $name) {
        return $cache;
    } elseif ('' === $value) { // 获取缓存
        return $cache->get($name);
    } elseif (is_null($value)) { // 删除缓存
        return $cache->rm($name);
    } else { // 缓存数据
        if (is_array($options)) {
            $expire = isset($options['expire']) ? $options['expire'] : NULL;
        } else {
            $expire = is_numeric($options) ? $options : NULL;
        }
        return $cache->set($name, $value, $expire);
    }
}

/**
 * 递归创建文件夹
 * @param type $path 需要创建的文件夹，不包括文件
 * @param type $chmod 权限
 * @return boolen 
 */
function mkdirs($path, $chmod = 0755) {
    return is_dir($path) or ( mkdirs(dirname($path), $chmod) and mkdir($path, $chmod));
}

?>
