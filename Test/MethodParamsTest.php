<?php
require "define.php";
require_once 'Utils\MethodParams.class.php';

use Utils\MethodParams;

/**
 * Class MethodParamsTest
 *
 * @author cbping
 */
class MethodParamsTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var MethodParams
     */
    private static $C = null;

    public static function setUpBeforeClass()
    {
        $_GET["test"] = "hello";
        $_POST["test"] = "posthello";
        $_GET["name"] = "name";
        $_GET["int"] = 5;
        $_POST["fileName"] = "fileName";

        self::$C = MethodParams::newInstance();

    }

    public function testGET()
    {

        //方法
        function check_string($arg_name)
        {
            echo $arg_name;
            return false;
        }

        //校验正确
        $str = self::$C->GetParams("test", null, function ($arg_name) {
            return true;
        });

        $this->assertTrue($str == "hello", "校验正确");

        //校验失败
        $str = self::$C->GetParams("test", "test", "check_string");
        $this->assertTrue($str == "test", "校验失败");

        $str = self::$C->GetParams(array(), "test", null);
        $this->assertTrue($str == "test", self::$C->getError());
        $this->assertTrue(self::$C->isErr(), self::$C->getError());

        //参数不存在
        $str = self::$C->GetParams("testNot", "test", "");
        $this->assertTrue($str == "test", "参数不存在");

        //校验方法为空或者不存在
        $str = self::$C->GetParams("test", "test", null);
        $this->assertTrue($str == "hello", "校验方法为空");

        $str = self::$C->GetParams("test", "test", "notExist");
        $this->assertTrue($str == "test", self::$C->getError());

        //正则校验
        $str = self::$C->GetParams("test", "test", "/hello/");
        $this->assertTrue($str == "hello", self::$C->getError());

        $str = self::$C->GetParams("test", "test", "/hello");
        $this->assertTrue($str == "test", self::$C->getError());

        $str = self::$C->GetParams("test", "test", "/hell$/");
        $this->assertTrue($str == "test", self::$C->getError());
    }

    public function testPOST()
    {
        //方法
        function check_int($arg_name)
        {
            echo $arg_name;
            return false;
        }

        //校验正确
        $str = self::$C->PostParams("test", null, function ($arg_name) {
            return true;
        });

        $this->assertTrue($str == "posthello", "校验正确");

        //校验失败
        $str = self::$C->PostParams("test", "test", "check_int");
        $this->assertTrue($str == "test", "校验失败");

        //参数不存在
        $str = self::$C->PostParams("testNot", "test", "");
        $this->assertTrue($str == "test", "参数不存在");

        //校验方法为空或者不存在
        $str = self::$C->PostParams("test", "test", null);
        $this->assertTrue($str == "posthello", "校验方法为空");

        $str = self::$C->PostParams("test", "test", "notExist");
        $this->assertTrue($str == "test", self::$C->getError());
    }

    public function testParams()
    {
        //校验正确
        $str = self::$C->Params("name", null, function ($arg_name) {
            return true;
        });

        $this->assertTrue($str == "name", $str . "校验正确");

        //校验正确
        $str = self::$C->Params("fileName", null, function ($arg_name) {
            return true;
        });

        $this->assertTrue($str == "fileName", $str . "校验正确");

        //校验正确
        $str = self::$C->Params("test", null, function ($arg_name) {
            return true;
        });

        $this->assertTrue($str == "hello", $str . "校验正确");

        //不存在的方法
        $notExist = 1000;
        $str = self::$C->Params("fileName", null, function ($arg_name) {
            return true;
        }, $notExist);

        $this->assertTrue($str == null, self::$C->getError());

        //方法参数类型不正确
        $str = self::$C->Params("fileName", null, function ($arg_name) {
            return true;
        }, array());

        $this->assertTrue($str == null, self::$C->getError());


    }


    public function testFilterInput()
    {
        //--coverage-html ./coverage
        $str = self::$C->FilterInput(INPUT_GET, 'noExist', "default");
        $this->assertTrue($str == "default", self::$C->getError());

        $res = filter_input(INPUT_GET, 'test');

        $str = self::$C->FilterInput(INPUT_GET, 'test', "default", FILTER_SANITIZE_STRING);
        $this->assertTrue($str == "hello", $res . $str . self::$C->getError());

        $str = self::$C->FilterInput(INPUT_GET, 'test', "default", FILTER_VALIDATE_INT);
        $this->assertTrue($str == "default", self::$C->getError());
    }

    public function testOther()
    {
        $this->assertTrue(MethodParams::newInstance() instanceof MethodParams, "实例");

    }

    public static function tearDownAfterClass()
    {
    }

}
