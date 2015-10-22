<?php

/**
 * 类加载器
 * 包括类库自动加载等
 *
 * @author cbping
 */
class Loader
{
    /** 类库子目录集合 基于$BaseDir_ @var array */
    private static $Cl_sub_dir_ = array();

    /**文件可能后缀名集合 @var array */
    private static $File_suffix_ = array();

    /** 基本目录(顶层目录) @var string */
    private static $BaseDir_ = BASEPATH;


    public static function setBaseDir($arg_dir)
    {
        if (is_dir($arg_dir))
            self::$BaseDir_ = $arg_dir;
    }

    /**
     * 设置类的加载目录集合
     * @param $arg_dir array
     */
    public static function setClassDir(array $arg_dir)
    {
        if (is_array($arg_dir))
            self::$Cl_sub_dir_ = $arg_dir;
    }

    /**
     * 追加目录到加载目录集合中
     * @param $arg_dir (array or string)
     */
    public static function appendClassDir($arg_dir)
    {
        if (is_string($arg_dir))
            self::$Cl_sub_dir_[] = $arg_dir;
        if (is_array($arg_dir))
            self::$Cl_sub_dir_ = array_merge(self::$Cl_sub_dir_, $arg_dir);
    }

    /**
     * 设置文件后缀名集合
     *
     * @param $arg_suffix
     */
    public static function setSuffix($arg_suffix)
    {
        if (is_array($arg_suffix))
            self::$File_suffix_ = $arg_suffix;
    }


    /**
     * 追加后缀名到文件后缀名集合中
     *
     * @param $arg_suffix (array or string)
     */
    public static function appendtSuffix($arg_suffix)
    {
        if (is_string($arg_suffix))
            self::$File_suffix_[] = $arg_suffix;
        if (is_array($arg_suffix))
            self::$File_suffix_ = array_merge(self::$File_suffix_, $arg_suffix);
    }

    /**
     * @param $arg_dir
     * @param $arg_suffix
     */
    public static function init($arg_dir, $arg_suffix)
    {
        self::setClassDir($arg_dir);
        self::setSuffix($arg_suffix);
    }

    /**
     * 类库自动加载
     *
     * 加载失败则会抛出异常，
     * 成功则返回。
     * @notice 务必正确设置 $Cl_sub_dir_r 和 $File_suffix_
     * @see $Cl_sub_dir_r
     * @see $File_suffix_
     * @param string $arg_class 对象类名
     * @return void
     * @throws Exception
     *
     */
    public static function autoload($arg_class)
    {
        $class = $arg_class;
        $file = '';

        if (false !== ($lastpos = strrpos($class, '\\'))) //处理命名空间
            $class = substr($class, $lastpos + 1);

        foreach (self::$Cl_sub_dir_ as $dir) {
            foreach (self::$File_suffix_ as $suffix) {
                $file = str_replace('\\', '/', self::$BaseDir_ . $dir . $class . $suffix);
                if (!is_file($file))
                    continue;
                require_once($file); //
                if (class_exists($arg_class))
                    return;
            }
        }

        throw new Exception('the class "' . $arg_class . '" is not found');
    }

}