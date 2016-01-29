<?php
require "define.php";
require "Registers/driver/File_d.class.php";
require_once "AppHelper.class.php";

use Registers\File_d;


/**
 * Class File_dTest
 */
class File_dTest extends PHPUnit_Framework_TestCase
{

    private $_val = "testValue";


    /**
     * 建立共享基境
     *
     * @beforeClass
     */
    public static function setUpSomeSharedFixtures()
    {
        if (is_dir('Cache'))
            rmdir('Cache'); //删除目录
        $help = AppHelper::Instance();
        $help->config("DATA_CACHE_COMPRESS", true);
        $help->config("DATA_CACHE_CHECK", true);
        $help->config("FILE_CACHE_PATH", "Cache");
        $help->config("FILE_CACHE_NAME_PREFIX", "FileCache");
    }

    public function testWrite()
    {

        $help = AppHelper::Instance();

        $drive = new File_d();
        $key = "testFile";
        $this->assertTrue($drive->enabled(), "is drive ok?");
        //写入缓存
        $drive->set($key, $this->_val);
        $this->assertTrue(is_file($help->config("FILE_CACHE_PATH")
            . DIRECTORY_SEPARATOR . $help->config("FILE_CACHE_NAME_PREFIX")
            . md5($key) . ".php"),
            "the cache file is exist"
        );

        //传入参数
        $options = array("prefix" => "newCache");
        $drive = new File_d($options);
        //写入缓存
        $drive->set($key, $this->_val . "newCache");
        $this->assertTrue(is_file($help->config("FILE_CACHE_PATH")
            . DIRECTORY_SEPARATOR . $options["prefix"]
            . md5($key) . ".php"),
            "the newCache file is exist"
        );


        //关闭校验和
        $help->config("DATA_CACHE_CHECK", false);
        $drive->set($key . "noCheck", $this->_val);
        $this->assertTrue(is_file($help->config("FILE_CACHE_PATH")
            . DIRECTORY_SEPARATOR . $options["prefix"]
            . md5($key) . ".php"),
            "the newCache file is exist"
        );

        //关闭压缩
        $help->config("DATA_CACHE_COMPRESS", false);
        $help->config("DATA_CACHE_CHECK", true);
        $drive->set($key . "noCompress", $this->_val);
        $this->assertTrue(is_file($help->config("FILE_CACHE_PATH")
            . DIRECTORY_SEPARATOR . $options["prefix"]
            . md5($key) . ".php"),
            "the newCache file is exist"
        );

        //都关闭
        $help->config("DATA_CACHE_COMPRESS", false);
        $help->config("DATA_CACHE_CHECK", false);
        $drive->set($key . "noAll", $this->_val);
        $this->assertTrue(is_file($help->config("FILE_CACHE_PATH")
            . DIRECTORY_SEPARATOR . $options["prefix"]
            . md5($key) . ".php"),
            "the newCache file is exist"
        );

        $help->config("DATA_CACHE_COMPRESS", true);
        $help->config("DATA_CACHE_CHECK", true);

    }

    /**
     * @depends testWrite
     */
    public function testRead()
    {
        $help = AppHelper::Instance();

        $drive = new File_d();
        $key = "testFile";
        $this->assertTrue($drive->enabled(), "is drive ok?(testRead)");
        $this->assertTrue($drive->Info() == $drive->Info(), "is drive ok?(testRead)");
        $this->assertEquals($this->_val, $drive->get($key), "get the cache with the key:" . $key);

        //传入参数
        $options = array("prefix" => "newCache");
        $drive = new File_d($options);
        $this->assertEquals($this->_val . "newCache", $drive->get($key), "get the newCache with the key:" . $key);

        //关闭校验和
        $this->assertEquals($this->_val, $drive->get($key . "noCheck"), "get the newCache with the key: noCheck" . $key);
        //关闭压缩
        $this->assertEquals($this->_val, $drive->get($key . "noCompress"), "get the newCache with the key: noCompress" . $key);
        //都关闭
        $this->assertEquals($this->_val, $drive->get($key . "noAll"), "get the newCache with the key: noAll" . $key);
        //校验出错
        $options = array("temp" => __DIR__ . "/Md5");
        $drive = new File_d($options);
        //传入时间，1秒，保证过期
        $drive->set($key . "expire", $this->_val, 1);
        sleep(2);
        //传入时间，1秒，保证过期
        $this->assertEquals(false, $drive->get($key . "expire"), "the key will be out of time ");
        //校验码出错
        $this->assertEquals(false, $drive->get($key . "testMd5"), "md5 error" . $key);
        //不存在的键
        $this->assertEquals(false, $drive->get($key), "the key not exist:" . $key);

        //TODO:
        \Registers\Driver::getInstance();
    }

    /**
     * @depends testRead
     */
    public function testUpdate()
    {
        $drive = new File_d();
        $key = "testFile";
        $drive->update($key, "newVal");
        $this->assertEquals("newVal", $drive->get($key), "update");
        //更新不存在的缓存
        $this->assertEquals(false, $drive->update("d%Noexist", "newVal"), "update the key which no exist");
    }

    /**
     * --coverage-html coverage
     * @depends testUpdate
     */
    public function testDelete()
    {
        $drive = new File_d();
        $key = "testFile";
        $drive->delete($key);
        $this->assertEquals(false, $drive->get($key), "the key has been deleted" . $key);

        //清除缓存
        $options = array("prefix" => "newCache");
        $drive = new File_d($options);
        $drive->clear();
        $this->assertEquals(false, $drive->get($key . "noCompress"), "the cache has been cleared");
        $this->assertEquals(false, $drive->get($key . "noCheck"), "the cache has been cleared");
        $this->assertEquals(false, $drive->get($key . "noAll"), "the cache has been cleared");
    }


//    public function testOther()
//    {
//
//
//
//    }

}



//                $content=file_get_contents($help->config("FILE_CACHE_PATH")
//            . DIRECTORY_SEPARATOR . "newCache"
//            . md5($key . "noCheck") . ".php");
//
////        echo substr($content,0,8);
////        echo "\n";
////        echo substr($content,8,12);
////        echo "\n";
////        echo substr($content,20,2);
//////        echo "\n";
//////        echo substr($content,22,32);
//////        echo "\n";
////        echo substr($content,22,-3);
////        echo "\n";
////        echo substr($content,-3,3);
////        echo "\n";
//
//       echo  unserialize(gzuncompress(substr($content,22,-3)));