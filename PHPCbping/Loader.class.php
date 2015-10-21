<?php

/**
 * 类加载器
 * 包括类库自动加载等
 *
 * @author cbping
 */
class Loader
{
    /** 类库目录集合@var array */
    private static $_cl_dir = array();

    /**文件可能后缀名集合 @var array */
    private static $_file_suffix = array();

    //   private static $_map_class = array();

    /**
     * 设置类的加载目录集合
     * @param $arg_dir array
     */
    public static function setClassDir(array $arg_dir)
    {
        if (is_array($arg_dir))
            self::$_cl_dir = $arg_dir;
    }

    /**
     * 追加目录到加载目录集合中
     * @param $arg_dir (array or string)
     */
    public static function appendClassDir($arg_dir)
    {
        if (is_string($arg_dir))
            self::$_cl_dir[] = $arg_dir;
        if (is_array($arg_dir))
            self::$_cl_dir = array_merge(self::$_cl_di, $arg_dir);
    }

    /**
     * 设置文件后缀名集合
     *
     * @param $arg_suffix
     */
    public static function setSuffix($arg_suffix)
    {
        if (is_array($arg_suffix))
            self::$_file_suffix = $arg_suffix;
    }


    /**
     * 追加后缀名到文件后缀名集合中
     *
     * @param $arg_suffix (array or string)
     */
    public static function appendtSuffix($arg_suffix)
    {
        if (is_string($arg_suffix))
            self::$_file_suffix[] = $arg_suffix;
        if (is_array($arg_suffix))
            self::$_file_suffix = array_merge(self::$_file_suffix, $arg_suffix);
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
     * @notice 务必正确设置 $_cl_dir 和 $_file_suffix
     * @see $_cl_dir
     * @see $_file_suffix
     * @param string $arg_class 对象类名
     * @return void
     * @throws Exception
     *
     */
    public static function autoload($arg_class)
    {
        // $arg_class = ucfirst($arg_class); //首字母大写
        $class = $arg_class;
        if (false !== ($lastpos = strrpos($class, '\\'))) //处理命名空间
            $class = substr($class, $lastpos + 1);
        // $class = str_replace('\\', '/', $class);
        foreach (self::$_cl_dir as $dir) {
            foreach (self::$_file_suffix as $suffix) {
                if (!is_file($dir . $class . $suffix))
                    continue;
                require_once($dir . $class . $suffix);
                if (class_exists($arg_class))
                    return;
            }
        }

        throw new Exception('the class "' . $arg_class . '" is not found');
    }

//    /**
//     * search for folders and subfolders with classes
//     *
//     * @param $className string
//     * @param $sub string[optional]
//     * @return string
//     */
//    function classFolder($className, $sub = "/") {
//        $dir = dir(CLASS_DIR.$sub);
//
//        if(file_exists(CLASS_DIR.$sub.$className.".class.php"))
//            return CLASS_DIR.$sub;
//
//        while(false !== ($folder = $dir->read())) {
//            if($folder != "." && $folder != "..") {
//                if(is_dir(CLASS_DIR.$sub.$folder)) {
//                    $subFolder = classFolder($className, $sub.$folder."/");
//
//                    if($subFolder)
//                        return $subFolder;
//                }
//            }
//        }
//        $dir->close();
//        return false;
//    }

}