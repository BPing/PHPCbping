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

    }

    public function testStartNormal()
    {

        $_GET["cmd"] = "";

        require_once 'PHPCbping/PHPCbping.class.php';
        //启动程序
        PHPCbping::start();

        $this->assertEquals("default.default", file_get_contents(dirname(__FILE__) . "/temp/testForCmd.text"));
    }

}