<?php

require "define.php";
require_once 'Loader.class.php';

/**
 * Class AutoloadTest
 */
class AutoloadTest extends PHPUnit_Framework_TestCase
{

    static $app_root = '';

    public static function setUpBeforeClass()
    {
        self::$app_root = __DIR__;
        Loader::init(array(self::$app_root . '/Library/'), array('.class.php'));
        spl_autoload_register("Loader::autoload");
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        spl_autoload_unregister("Loader::autoload");
    }

    /**
     * 正常调用自动加载
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the class is load
     */
    public function testAutoload()
    {
        Loader::setClassDir(array(self::$app_root . '/Library/'));
        Loader::setSuffix(array('.class.php'));

        $cl = new TestClassLoad();
        throw new Exception("the class is load");
    }

    /**
     * 类不存在
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the class "ClassNotExist" is not found
     */
    public function testClassNotExist()
    {
        Loader::init(array(self::$app_root . '/Library/'), array('.class.php'));
        $cl = new ClassNotExist();
    }

    /**
     * 后缀名没有配置正确
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the class "TestInterface" is not found
     */
    public function testInterfaceNotExist()
    {
        $cl = new TestInterface();
    }

    /**
     * 添加相应的后缀名
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the class "TestInterface" is found
     */
    public function testInterfaceExist()
    {
        Loader::appendtSuffix('.interface.php');
        $cl = new TestInterface();
        throw new Exception("the class \"TestInterface\" is found");
    }


    /**
     * 目录包含进来
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the class "TestSubDir" is not found
     */
    public function testSubDirNotExist()
    {
        $cl = new TestSubDir();
    }

    /**
     * 包含相应的目录
     *
     * @expectedException        Exception
     * @expectedExceptionMessage the class "TestSubDir" is found
     */
    public function testSubDirExist()
    {
        Loader::appendClassDir(self::$app_root . '/Library/SubDir/');
        $cl = new TestSubDir();
        throw new Exception("the class \"TestSubDir\" is found");
    }

    /**
     * @expectedException        Exception
     * @expectedExceptionMessage the class "NameClass" is found
     */
    public function testNameClassExist()
    {
        Loader::appendClassDir(self::$app_root . '/Library/SubDir/');
        $cl = new SubDir\NameClass();
        throw new Exception("the class \"NameClass\" is found");
    }

}