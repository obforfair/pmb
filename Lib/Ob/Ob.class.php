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
 * ThinkPHP 引导类
 */
class Ob {

    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    static public function appException($e) {
        if (APP_DEBUG) {
            $msg = $e->getTraceAsString();
        } else {
            $msg = '';
        }
        jsonReturn(E_ERROR, $msg);
    }

    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    static public function appError($errno, $errstr, $errfile, $errline) {
        if (APP_DEBUG) {
            $msg = "file:" . $errfile . "。errno:" . $errno . '。errline:' . $errline . '。errorMsg:' . $errstr;
        } else {
            $msg = '';
        }
        jsonReturn(E_ERROR, $msg);
    }

    // 致命错误捕获
    static public function fatalError() {
        Log::save();
        if ($e = error_get_last()) {
            header('HTTP/1.1 500 Interior Error');
            header('Status:500 Interior Error');
            switch ($e['type']) {
                case E_ERROR:
                case E_PARSE:
                case E_CORE_ERROR:
                case E_COMPILE_ERROR:
                case E_USER_ERROR:
                    ob_end_clean();
                    self::halt($e);
                    break;
            }
        }
    }

    /**
     * 错误输出
     * @param mixed $error 错误
     * @return void
     */
    static public function halt($error) {
        dd(1);
        $e = array();
        if (APP_DEBUG || IS_CLI) {
            //调试模式下输出错误信息
            if (!is_array($error)) {
                $trace = debug_backtrace();
                $e['message'] = $error;
                $e['file'] = $trace[0]['file'];
                $e['line'] = $trace[0]['line'];
                ob_start();
                debug_print_backtrace();
                $e['trace'] = ob_get_clean();
            } else {
                $e = $error;
            }
            if (IS_CLI) {
                exit(iconv('UTF-8', 'gbk', $e['message']) . PHP_EOL . 'FILE: ' . $e['file'] . '(' . $e['line'] . ')' . PHP_EOL . $e['trace']);
            }
        } else {
            //否则定向到错误页面
            $error_page = C('ERROR_PAGE');
            if (!empty($error_page)) {
                redirect($error_page);
            } else {
                $message = is_array($error) ? $error['message'] : $error;
                $e['message'] = C('SHOW_ERROR_MSG') ? $message : C('ERROR_MESSAGE');
            }
        }
        // 包含异常页面模板
        $exceptionFile = C('TMPL_EXCEPTION_FILE', null, THINK_PATH . 'Tpl/think_exception.tpl');
        include $exceptionFile;
        exit;
    }

}
