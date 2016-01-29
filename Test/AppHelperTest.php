<?php
require "define.php";
require "AppHelper.class.php";


class AppHelperTest extends PHPUnit_Framework_TestCase
{

    public function pathHandler($path)
    {
        return str_replace('\\', '/', $path);
    }


    public function testLoadConfig()
    {

        //加载后缀 .ini 类型
        $config = AppHelper::loadConfig(__DIR__ . "/temp/config.ini");
        $this->assertEquals(1, $config["file_uploads"], "load the config with suffix :.ini");
        //加载后缀 .xml 类型
        $config = AppHelper::loadConfig(__DIR__ . "/temp/config.xml");
//        print_r($config);
        $this->assertEquals("test", $config["directory"], "load the config with suffix :.ini");
        //加载后缀 .json 类型
        $config = AppHelper::loadConfig(__DIR__ . "/temp/config.json");
//        print_r($config);
        $this->assertEquals("test", $config["name"], "load the config with suffix :.json");
        //用外部解析办法解析配置文件
        function  parse($name)
        {
            return array("name" => $name);
        }

        $config = AppHelper::loadConfig(__DIR__ . "/temp/config.exit", "parse");
        $this->assertEquals($this->pathHandler(__DIR__ . "/temp/config.exit"), $config["name"], "load the config with own parse's method ");

        //加载不存在解析办法的
        try {
            $config = AppHelper::loadConfig(__DIR__ . "/temp/config.exit");
            $this->assertTrue(false, "load the file without  parse's method");

        } catch (Exception $e) {
            $this->assertTrue(true, $e->getMessage());
        }

    }

    public function testConfig()
    {
        try {
            $ah = AppHelper::Instance();

            $config = include "App.config.php";
            //读取系统配置
            $this->assertEquals($config["USER_CONFIG_FILE_PATH"], $ah->config("USER_CONFIG_FILE_PATH"), "load system's config fail");
            //读取不存在
            $this->assertTrue(null === $ah->config("NO_EXIST"), "read the attributes which no exist");
            //添加不存在的配置属性
            $ah->config("NO_EXIST", True);
            $this->assertTrue($ah->config("NO_EXIST"), "add the attributes which no exist");
            //更变已存在的值
            $ah->config("NO_EXIST", False);
            $this->assertTrue(!$ah->config("NO_EXIST"), "update the attributes which no exist");


        } catch (\Exception $e) {
            $this->assertTrue(false, "some error  happen when run the test testConfig: " . $e->getMessage());
        }

    }
}

