<?php

/**
 * 系统单元测试
 *
 * Class CoreStartUpTest
 */
class CoreStartUpTest extends PHPUnit_Framework_TestCase
{

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        require_once 'define.php';
        require_once 'AppHelper.class.php';
        require_once 'PHPCbping.class.php';

        AppHelper::Instance()->config("APP_CTRL", 'TestControllers1/');
        AppHelper::Instance()->config("DEFAULT_CMD", 'DefaultControllerCore');
        $CPath = APPPATH . AppHelper::Instance()->config("APP_CTRL");

        if (!is_dir($CPath))
            rename(dirname(__FILE__) . "/TestControllers1/", $CPath);
        chmod($CPath, 0777);

        if (!is_dir(APPPATH . "temp/"))

            mkdir(APPPATH . "temp/", 0777);
    }

    public function testStartNormal()
    {
        try {
            $_GET["cmd"] = "";
            //启动程序
            PHPCbping::start();
            $this->assertEquals("default.default", file_get_contents(APPPATH . "/temp/testForCmd.text"));

            $_GET["cmd"] = "test.testCore";
            PHPCbping::start();
            $this->assertEquals("test.test", file_get_contents(APPPATH . "/temp/testForCmd.text"));

            //输出异常
            $_GET["cmd"] = "test.testCore";
            $_GET["exc"] = "echo_exc";
            PHPCbping::start();
            $this->assertEquals("echo_exc", file_get_contents(APPPATH . "/temp/testForCmd.text"));

            //一般异常
            $_GET["cmd"] = "test.testCore";
            $_GET["exc"] = "exc";
            PHPCbping::start();
            $this->assertEquals("exc", file_get_contents(APPPATH . "/temp/testForCmd.text"));

            $_GET["cmd"] = "noHaveCore";
            PHPCbping::start();
            $_GET["cmd"] = "NocmdCore";
            PHPCbping::start();
            $_GET["cmd"] = "NoClassExistCore";
            PHPCbping::start();
            //测试控制过滤
            AppHelper::Instance()->config("CMD_FILTER", '/ControllerCore$/');
            $_GET["cmd"] = "DefaultControllerCore";
            PHPCbping::start();
            $this->assertEquals("default.default", file_get_contents(APPPATH . "/temp/testForCmd.text"));

        } catch (Exception $e) {
            log_message(LOG_ERR, $e->getMessage());
            echo $e->getMessage();
            $this->assertEquals(false, true);
        }

    }


    /**
     * 清理测试环境
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        AppHelper::Instance()->config("APP_CTRL", 'TestControllers1/');
        $CPath = APPPATH . AppHelper::Instance()->config("APP_CTRL");
        rename($CPath, dirname(__FILE__) . "/TestControllers1/");
        unlink(APPPATH . "/temp/testForCmd.text");
    }

}