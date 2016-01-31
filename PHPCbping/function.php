<?php

// ------------------------------------------------------------------------
if (!function_exists('throw_e')) {
    /**
     * 抛出异常处理
     *
     * @param  string $msg 异常消息
     * @param integer $code 异常代码 默认为0
     * @param string $exc 异常类
     * @throws Exception
     */
    function throw_e($msg, $code = 0, $exc = '')
    {
        if (null === $exc || !is_string($exc) || '' === $exc)
            throw new \Exception($msg, $code);
        $exc = $exc . 'Exception';
        throw new $exc($msg, $code);
    }
}


// ------------------------------------------------------------------------

if (!function_exists('log_message')) {
    /**
     * 日志记录
     *
     * 级别采用php库的全局变量  支持级别 LOG_WARNING LOG_ERR LOG_INFO LOG_DEBUG
     * 分别映射到日志句柄{@link LogI }对应方法：logWarn logError logInfo logDebug
     *
     * @param $level int 支持级别 LOG_WARNING LOG_ERR LOG_INFO LOG_DEBUG
     * @param $message
     * @param LogI $newLog null 对应核心的日志接口：LogI
     * @return old or null
     * @see LogI
     */
    function log_message($level, $message, $newLog = null)
    {
        static $_log;
        if ($newLog instanceof LogI) {
            $old = $_log;
            $_log = $newLog;
        }
        if (empty($_log))
            return;

        if ($level == LOG_ERR) {
            $_log->logError($message);
        }
        if ($level == LOG_WARNING) {
            $_log->logWarn($message);
        }
        if ($level == LOG_INFO) {
            $_log->logInfo($message);
        }
        if ($level == LOG_DEBUG) {
            $_log->logDebug($message);
        }
//        echo $message;
        return isset($old) ? $old : null;
    }
}

// ------------------------------------------------------------------------
if (!function_exists('get_config')) {
    /**
     *
     */
    function get_config()
    {

    }
}

// ------------------------------------------------------------------------
if (!function_exists('_exception_handle')) {
    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    function _exception_handle($e)
    {
        $error = array();
        $error['message'] = $e->getMessage();
        $trace = $e->getTrace();
        if ('throw_e' == $trace[0]['function']) {
            $error['file'] = $trace[0]['file'];
            $error['line'] = $trace[0]['line'];
        } else {
            $error['file'] = $e->getFile();
            $error['line'] = $e->getLine();
        }
        $error['trace'] = $e->getTraceAsString();
//        ob_end_clean();
        // 发送404信息
        set_status_header(404);
        halt($error);
    }
}


// ------------------------------------------------------------------------
if (!function_exists('_error_handle')) {
    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    function _error_handle($errno, $errstr, $errfile, $errline)
    {
        $is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $errno) === $errno);
        if ($is_error) {
            ob_end_clean();
            $errorStr = "$errstr " . $errfile . " 第 $errline 行.";
            // 发送500信息
            set_status_header(500);
            halt($errorStr);
        }

    }
}


// ------------------------------------------------------------------------
if (!function_exists('_finish_handle')) {
    /**
     * 程序结束处理
     *
     * 包括致命错误捕获处理
     *
     */
    function _finish_handle()
    {
        if ($e = error_get_last()) {
            $is_error = (((E_ERROR | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $e['type']) === $e['type']);
            if ($is_error) {
                ob_end_clean();
                set_status_header(500);
                halt($e);
            }
        }
    }
}
// ------------------------------------------------------------------------
if (!function_exists('halt')) {
    /**
     *
     * @param $error
     */
    function halt($error)
    {
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
        $msg = $e['message'] . PHP_EOL . 'FILE: ' . $e['file'] . '(' . $e['line'] . ')' . PHP_EOL . $e['trace'];
        log_message(LOG_ERR, $msg);
        exit;
    }
}


// ------------------------------------------------------------------------
if (!function_exists('set_status_header')) {
    /**
     * 设置头部状态
     *
     * @param    int    the status code
     * @param    string
     * @return    void
     */
    function set_status_header($code = 200, $text = '')
    {
        if (empty($code) OR !is_numeric($code)) {
            log_message(LOG_ERR, "状态码非数字");
            return;
        }
        if (empty($text)) {
            is_int($code) OR $code = (int)$code;
            $stati = array(
                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',

                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',

                400 => 'Bad Request',
                401 => 'Unauthorized',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                422 => 'Unprocessable Entity',

                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported'
            );

            if (!isset($stati[$code])) {
                log_message(LOG_ERR, '没有此状态码存在，请检查你的状态码是否正确或者提供状态码描述信息');
                return;
            }
            $text = $stati[$code];
        }

        $server_protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header($server_protocol . ' ' . $code . ' ' . $text, TRUE, $code);
        header('Status: ' . $code . ' ' . $text, TRUE);
    }
}

// ------------------------------------------------------------------------
if (!function_exists('is_php')) {
    /**
     * php的版本号是否大于等于提供的版本号
     *
     * @param    string
     * @return    bool    如果大于或等于则返回 true
     */
    function is_php($version)
    {
        static $_is_php;
        $version = (string)$version;

        if (!isset($_is_php[$version])) {
            $_is_php[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $_is_php[$version];
    }
}