<?php
require_once 'Loader.class.php';
require_once 'AppHelper.class.php';
require_once 'function.php';

/**
 * Class PHPCbping
 */
class PHPCbping
{
    /**
     * 启动
     */
    public static function start()
    {

        /*
        * ------------------------------------------------------
        *  设置时区
        * ------------------------------------------------------
        */

        date_default_timezone_set("Asia/Shanghai");

        /*
        * ------------------------------------------------------
        * 安全程序：关掉魔法引号，过滤全局变量
        * ------------------------------------------------------
        */

        if (!is_php('5.4')) {
            ini_set('magic_quotes_runtime', 0);

            if ((bool)ini_get('register_globals')) {
                $_protected = array(
                    '_SERVER',
                    '_GET',
                    '_POST',
                    '_FILES',
                    '_REQUEST',
                    '_SESSION',
                    '_ENV',
                    '_COOKIE',
                    'GLOBALS',
                    'HTTP_RAW_POST_DATA',
                    '_protected',
                    '_registered'
                );

                $_registered = ini_get('variables_order');
                foreach (array('E' => '_ENV', 'G' => '_GET', 'P' => '_POST', 'C' => '_COOKIE', 'S' => '_SERVER') as $key => $superglobal) {
                    if (strpos($_registered, $key) === FALSE) {
                        continue;
                    }

                    foreach (array_keys($$superglobal) as $var) {
                        if (isset($GLOBALS[$var]) && !in_array($var, $_protected, TRUE)) {
                            $GLOBALS[$var] = NULL;
                        }
                    }
                }
            }
        }

        //全局变量定义
        require_once "define.php";

        /*
        * ------------------------------------------------------
        *  异常处理，错误处理等,记录错误日志
        * ------------------------------------------------------
        */

        register_shutdown_function("_finish_handle");
        set_exception_handler("_exception_handle");
        set_error_handler("_error_handle");

        /*
        * ------------------------------------------------------
        *  注册自动加载方法
        * ------------------------------------------------------
        */

        spl_autoload_register("Loader::autoload");
        Loader::setClassDir((array)AppHelper::Instance()->config("APP_AUTOLOAD_PATH"));
        Loader::setSuffix((array)AppHelper::Instance()->config("CLASS_FILE_SUFFIX"));

        /*
        * ------------------------------------------------------
        * ini 设置
        * ------------------------------------------------------
        */

        //默认字符编码为UTF-8
        ini_set('default_charset', 'UTF-8');

        //日志初始
        log_message(LOG_INFO, "初始处理完毕", \Utils\UtilFactory::getLogHandle());

        //运行核心程序
        log_message(LOG_INFO, "运行核心程序................");
        CommandHandler::run();
    }


}