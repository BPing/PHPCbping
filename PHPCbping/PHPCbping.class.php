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
        //注册钩子:自动加载，异常处理，错误处理等
        spl_autoload_register("Loader::autoload");
        register_shutdown_function("_finish_handle");
        set_exception_handler("_exception_handle");
        set_error_handler("_error_handle");

        CommandHandler::run();
    }
}