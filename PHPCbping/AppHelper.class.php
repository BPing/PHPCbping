<?php
defined("EXT_NOT_SUPPORT") or define("EXT_NOT_SUPPORT", "没有支持的解析函数存在");
defined("FILE_NOT_FOUND") or define("FILE_NOT_FOUND", "配置文件不存在");

/**
 * 应用程序助手类，处理应用配置信息
 *
 * Class AppHelper
 *
 * @author cbping
 */
class AppHelper
{
    /**
     * 配置项数组
     * @var array
     */
    private static $config_ = array();

    private static $instance_;
    /**
     * 应用目录
     * @var string
     */
    private static $_AppPath = APPPATH;

    /**
     *
     * @var string
     */
    private static $_SysPath = SYSPATH;

    private function __construct()
    {
    }

    public static function Instance()
    {
        if (empty(self::$instance_))
            self::$instance_ = new self();
        return self::$instance_;

    }

    /** 加载系统配置和用户配置 */
    private function _loadConfig()
    {
        self::$config_ = self::loadConfig(self::$_SysPath . 'App.config.php');
        if (isset(self::$config_["USER_CONFIG_FILE_PATH"])) {
            $arr = self::loadConfig(self::$_AppPath . self::$config_["USER_CONFIG_FILE_PATH"]); //加载用户配置
            if (is_array($arr))
                self::$config_ = array_merge(self::$config_, $arr);
        }
    }

    /**
     * 获取或者设置配置信息
     *
     * @param  $arg_key string 键名
     * @param null $arg_value string  不为空的则是设置新的值
     * @return mixed
     */
    public function config($arg_key, $arg_value = null)
    {
        //加载配置
        if (empty(self::$config_))
            $this->_loadConfig();
        $arg_key = strtoupper($arg_key);
        //不为空的则是设置新的值
        if (!is_null($arg_value)) {
            self::$config_[$arg_key] = $arg_value;
            return null;
        }
        if (isset(self::$config_[$arg_key]))
            return self::$config_[$arg_key];

        return null;
    }


    /**
     * 加载配置文件 支持格式转换 仅支持一级配置
     *
     * @param string $arg_file 配置文件名
     * @param string $arg_parse 配置解析方法 有些格式需要用户自己解析
     * @return mix
     * @throws Exception
     */
    public static function loadConfig($arg_file, $arg_parse = null)
    {
        $arg_file = str_replace('\\', '/', $arg_file);
        if (!is_file($arg_file))
            self::_throw(FILE_NOT_FOUND . ":" . $arg_file);
        $ext = pathinfo($arg_file, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'php':
                return include $arg_file;
            case 'ini':
                return parse_ini_file($arg_file);
//            case 'yaml':
//                return yaml_parse_file($file);
            case 'xml':
                return (array)simplexml_load_file($arg_file);
            case 'json':
                return json_decode(file_get_contents($arg_file), true);
            default:
                if (function_exists($arg_parse)) {
                    return $arg_parse($arg_file);
                } else {
                    self::_throw(EXT_NOT_SUPPORT . ':' . $ext);
                }
        }
    }


//    private static function _ensure($arg_expr, $arg_msg, $arg_code = 0)
//    {
//        if (!$arg_expr)
//            self::_throw($arg_msg, $arg_code);
//    }

    /**
     * 抛出异常处理
     *
     * @param  string $msg 异常消息
     * @param integer $code 异常代码 默认为0
     * @throws Exception
     */
    private static function _throw($msg, $code = 0)
    {
        throw new \Exception($msg, $code);
    }
}



