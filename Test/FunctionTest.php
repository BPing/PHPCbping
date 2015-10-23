<?php
require "define.php";
require_once "function.php";
require_once 'Log.interface.php';
require_once 'Utils\Logs.class.php';
require_once 'Utils\UtilFactory.class.php';

class FunctionTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        log_message(LOG_DEBUG, "start up the log", \Utils\UtilFactory::getLogHandle());

        set_exception_handler("_exception_handle");
        register_shutdown_function("_finish_handle");
    }

    /**
     * 测试错误处理
     */
    public function testError()
    {

    //    set_status_header("505");
//        set_error_handler("_error_handle");
//        trigger_error("hello world", E_USER_ERROR);
    }

    /**
     *测试异常处理
     */
    public function testException()
    {

        //throw new \Exception("exception");

    }

    /**
     *测试致命错误
     */
    public function testFinish()
    {

    }
}