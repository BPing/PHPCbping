<?php
require "define.php";
require_once "AppHelper.class.php";
require_once "Loader.class.php";
require_once "function.php";
require_once "CommandHandler.class.php";
require_once "Context.class.php";
require_once "Controller.absclass.php";
require_once "ControlResolver.class.php";
require_once "Exceptions/EchoException.class.php";
require_once "Exceptions/ResolverException.class.php";
require_once "TestControllers/DefaultController.class.php";
require_once "Utils/MethodParams.class.php";

/**
 * 命令单元测试
 *
 * Class CommandTest
 */
class CommandTest extends PHPUnit_Framework_TestCase
{

    static $_APP_CTRL;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        ControlResolver::setAppPath(dirname(__FILE__) . '/');
        self::$_APP_CTRL = AppHelper::Instance()->config("APP_CTRL");
        AppHelper::Instance()->config("APP_CTRL", "TestControllers");
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        ControlResolver::setAppPath(APPPATH);
        AppHelper::Instance()->config("APP_CTRL", self::$_APP_CTRL);
        AppHelper::Instance()->config("CMD_FILTER", ''); //恢复默认
    }

    /**
     * 文件不存在异常
     *
     * @throws \Exceptions\ResolverException
     */
    public function testNoCommandFile()
    {
        $this->setExpectedException(
            'Exceptions\\ResolverException', "Command file 'noHave' not found"
        );
        $_GET["cmd"] = "noHave";
        $context = new Context();
        $ctrl_r = new ControlResolver();
        $ctrl = $ctrl_r->getController($context);
    }

    /**
     * 类不存在异常
     *
     * @expectedException        Exceptions\ResolverException
     * @expectedExceptionMessage Command 'Nocmd' is not a command
     */
    public function testNoCmd()
    {
        $_GET["cmd"] = "Nocmd";
        $context = new Context();
        $ctrl_r = new ControlResolver();
        $ctrl = $ctrl_r->getController($context);
    }

    /**
     * 不是控制器异常
     *
     * @expectedException        Exceptions\ResolverException
     * @expectedExceptionMessage Command 'NoClassExist' not found
     */
    public function testNoClassExist()
    {
        $_GET["cmd"] = "NoClassExist";
        $context = new Context();
        $ctrl_r = new ControlResolver();
        $ctrl = $ctrl_r->getController($context);
    }

    /**
     * 控制器无法通过过滤器异常
     * @expectedException        Exceptions\ResolverException
     * @expectedExceptionMessage Command cannot pass filter
     */
    public function testNoFilter()
    {
        AppHelper::Instance()->config("CMD_FILTER", '/Controller$/');
        $_GET["cmd"] = "NoClassExist";
        $context = new Context();
        $ctrl_r = new ControlResolver();
        $ctrl = $ctrl_r->getController($context);
    }

    /**
     * 主要测试，测试正常情况
     */
    public function testMain()
    {
        AppHelper::Instance()->config("CMD_FILTER", ''); //恢复默认
        $_POST["cmd"] = "test.test";
        CommandHandler::run();
        $this->assertEquals("test.test", file_get_contents(dirname(__FILE__) . "/temp/testForCmd.text"));

        $_GET["cmd"] = "";
        CommandHandler::run();
        $this->assertEquals("default.default", file_get_contents(dirname(__FILE__) . "/temp/testForCmd.text"));

        //输出异常
        $_GET["cmd"] = "test.test";
        $_GET["exc"] = "echo_exc";
        CommandHandler::run();
        $this->assertEquals("echo_exc", file_get_contents(dirname(__FILE__) . "/temp/testForCmd.text"));

        //一般异常
        $_GET["cmd"] = "test.test";
        $_GET["exc"] = "exc";
        CommandHandler::run();
        $this->assertEquals("exc", file_get_contents(dirname(__FILE__) . "/temp/testForCmd.text"));

        $_GET["cmd"] = "noHave";
        CommandHandler::run();
        $_GET["cmd"] = "Nocmd";
        CommandHandler::run();
        $_GET["cmd"] = "NoClassExist";
        CommandHandler::run();
        //测试控制过滤
        AppHelper::Instance()->config("CMD_FILTER", '/Controller$/');
        $_GET["cmd"] = "DefaultController";
        CommandHandler::run();
        $this->assertEquals("default.default", file_get_contents(dirname(__FILE__) . "/temp/testForCmd.text"));
    }
}