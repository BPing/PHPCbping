<?php
require "define.php";
require_once "Context.class.php";
require_once "Exceptions/EchoException.class.php";
require_once "Utils/MethodParams.class.php";

class ContextTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $context Context
     */
    static $context;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$context = new Context();
    }


    public function  testContext()
    {
        $_GET["cmd"] = "test";
        $this->assertEquals("test", self::$context->Params("cmd"));
    }

    /**
     * 输出类型异常
     *
     * @expectedException        \Exceptions\EchoException
     * @expectedExceptionMessage Output type exception
     */
    public function testEchoExc()
    {
        self::$context->json_echo(new Context());
    }

    /**
     * 调用不存在的方法
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the method don't exist
     */
    public function testNoMethod()
    {
        self::$context->NoMethod();
    }

    /**
     * @depends  testContext
     * @depends  testEchoExc
     * @depends  testNoMethod
     */
    public function testEcho()
    {
        self::$context->err_echo("some test");
        //  self::$context->json_echo(array("test" => "nihao"));
    }

}