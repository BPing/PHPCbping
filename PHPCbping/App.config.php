<?php

/**
 * 系统配置
 *
 * 用户配置可以覆盖默认的系统配置选项
 * @notice 严禁去掉配置选项
 * @author cbping
 */

//全局变量定义
defined("ON") or define("ON", true);
defined("OFF") or define("OFF", false);


return array(

    //**用户配置文件目录*/
    //用户配置不能覆盖此配置
    "USER_CONFIG_FILE_PATH" => 'Config/config.php',

    // 应用目录
    "APP_ROOT" => "",

    //控制器目录，相对APP_ROOT
    //也是命名空间
    "APP_CTRL" => "Controllers",

    //默认的控制器
    //文件名对应为 DefaultController.class.php
    //@used 命令解析器
    "DEFAULT_CMD" => "DefaultController",

    //控制器过滤
    //如果为空，则是不过滤
    //@used 命令解析器
    "CMD_FILTER" => "",

    //控制器文件后缀名
    "CTRL_FILE_SUFFIX" => ".class.php",

    //类的文件后缀名集合
    //@type array
    //@used 类自动记载器
    "CLASS_FILE_SUFFIX" => array(".class.php", ".absclass.php", ".interface.php"),

    // 是否进行数据压缩
    // 仅当此变量为打开和压缩函数存在时
    // 才会对数据进行压缩处理
    // @used 文件缓存
    "DATA_CACHE_COMPRESS" => ON,

    //是否开启数据校验
    // @used 文件缓存
    "DATA_CACHE_CHECK" => ON,

    //文件缓冲路径
    // @used 文件缓存
    "FILE_CACHE_PATH" => "Cache",

    //文件名前缀
    // @used 文件缓存
    "FILE_CACHE_NAME_PREFIX" => "FileCache",

    //自动加载类目录;
    //将会在定义的目录中查找相应的类，
    //如果找到则引进；
    //@notice 路径相对于工程目录
    //@type array
    //@used 类自动记载器
    "APP_AUTOLOAD_PATH" => array(),


);


