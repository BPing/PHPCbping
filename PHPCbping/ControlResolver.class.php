<?php

/**
 * Class ControlResolver
 * 控制器解析器
 * @author cbping
 */
class ControlResolver
{
    //默认控制器
    private static $_default_cmd;

    //控制器基类
    private static $_base_cmd;

    //控制器命名空间（也是主目录）
    private static $_ctrl_namespace;

    /**
     * 应用目录
     * @var string
     */
    private static $_AppPath = APPPATH;

    function __construct()
    {
        self::$_ctrl_namespace = trim(AppHelper::Instance()->config("APP_CTRL"), ' \\/');
        self::$_default_cmd = trim(AppHelper::Instance()->config("DEFAULT_CMD"), ' \\/');
        self::$_base_cmd = new ReflectionClass("\\Controller");
        //  self::$_AppPath = defined(APPPATH) ? APPPATH : '';
    }

    /**
     * @param $arg_path
     */
    public static function setAppPath($arg_path)
    {
        if (is_dir($arg_path)) {
            self::$_AppPath = $arg_path;
        }
    }

    /**
     * 解析命令新建对应命令控制器实例
     *
     * @param Context $arg_context 上下文 （请求内容）
     *
     * @return Controller
     * @throws \Exceptions\ResolverException
     */
    function getController(Context $arg_context)
    {
        $cmd = $arg_context->Params("cmd");
        $step = DIRECTORY_SEPARATOR;
        if (!$cmd) {
            $cmd = self::$_default_cmd;
        }
        //校验命令格式 TODO:可以在Params上校验
        $cmd_filter = AppHelper::Instance()->config("CMD_FILTER");
        if ($cmd_filter && preg_match($cmd_filter, $cmd) != 1) {
            throw new \Exceptions\ResolverException("Command cannot pass filter");
        }
        //如果存在‘.’ 则替换成文件分隔符，
        //实现控制器目录下多级组合
        $cmd = trim(str_replace(array("."), $step, $cmd));
        //应用根目录,控制器目录，文件后缀名
        $app_root = rtrim(self::$_AppPath, ' \\/');
        $app_ctrl = self::$_ctrl_namespace;
        $ctrl_suffix = AppHelper::Instance()->config("CTRL_FILE_SUFFIX");

        //构建文件目录和类
        $file_path = $app_root . $step . $app_ctrl . $step . $cmd . $ctrl_suffix;
        $class_name = "$app_ctrl\\" . (strripos($cmd, $step) ? substr($cmd, strripos($cmd, $step) + 1) : $cmd);
//        echo "\n", $file_path, "\n", $class_name, "\n";
        if (!file_exists($file_path)) {
            throw new \Exceptions\ResolverException("Command file '$cmd' not found");
        }
        @require_once("$file_path");
        if (!class_exists("$class_name")) {
            throw new \Exceptions\ResolverException("Command '$cmd' not found");
        }
        $cmd_class = new ReflectionClass("$class_name");
        if (!$cmd_class->isSubclassOf(self::$_base_cmd)) {
            throw new \Exceptions\ResolverException("Command '$cmd' is not a command");
        }

        return $cmd_class->newInstance();
    }

}

