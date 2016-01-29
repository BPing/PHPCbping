<?php

namespace Registers;

require "Driver.absclass.php";


defined("DATA_CACHE_COMPRESS") or define("DATA_CACHE_COMPRESS", 1);
defined("DATA_CACHE_CHECK") or define("DATA_CACHE_CHECK", 4);

/**
 * 文件类型缓存类
 */
class File_d extends Driver
{

    public function __construct($arg_options = array())
    {
        parent::__construct($arg_options);
        if (!isset($this->_options['temp'])) {
            $this->_options['temp'] = $this->_appHelper->config("FILE_CACHE_PATH");
        }
        if (!isset($this->_options['prefix']))
            $this->_options['prefix'] = $this->_appHelper->config("FILE_CACHE_NAME_PREFIX");
        if (substr($this->_options['temp'], -1) != '/')
            $this->_options['temp'] .= '/';
        // 创建应用缓存目录
        if (!is_dir($this->_options['temp'])) {
            mkdir($this->_options['temp']);
        }
    }

    /**
     * 获取文件名
     * @param string $arg_key key 键名
     * @return string
     */
    private function _filename($arg_key)
    {
        $arg_key = md5($arg_key);//
        $filename = $this->_options['prefix'] . $arg_key . '.php';
        return $this->_options['temp'] . $filename;
    }

    /**
     * 读取缓存
     * @param string $arg_key
     * @return bool|mixed|string
     */
    public function  get($arg_key)
    {
        $filename = $this->_filename($arg_key);
        if (!is_file($filename)) {
            return false;
        }
        $content = file_get_contents($filename);
        if (false !== $content) {
            $expire = (int)substr($content, 8, 12);
            if ($expire != 0 && time() > filemtime($filename) + $expire) {
                //缓存过期删除缓存文件
                unlink($filename);
                return false;
            }
            $status = $expire = (int)substr($content, 20, 2);
            //是否开启校验
            if (($status & DATA_CACHE_CHECK) != 0) {

                $check = substr($content, 22, 32);
                $content = substr($content, 54, -3);
                if ($check != md5($content)) {//校验错误
                    return false;
                }
            } else {
                $content = substr($content, 22, -3);
            }
            //是否启用数据压缩
            if (($status & DATA_CACHE_COMPRESS) != 0) {
                //不存在解压函数，则返回失败
                if (!function_exists('gzuncompress')) {
                    return false;
                }
                //解压数据
                $content = gzuncompress($content);
            }
            $content = unserialize($content);
            return $content;
        }

        return false;
    }

    /**
     * 写入缓存
     *
     * @param string $arg_key
     * @param mixed $arg_value
     * @param int $arg_expire 必须为整数，单位为秒
     * @return boolean
     */
    public function  set($arg_key, $arg_value, $arg_expire = null)
    {
        if (is_null($arg_expire) || !is_int($arg_expire) || $arg_expire < 0) {
            $arg_expire = 0;
        }
        //记录状态
        //$status&1=true 则是开启压缩，$status&1=true，则是开启校验
        $status = 0;
        $filename = $this->_filename($arg_key);
        $data = serialize($arg_value);
        if ($this->_appHelper->config("DATA_CACHE_COMPRESS") && function_exists('gzcompress')) {
            //数据压缩
            $data = gzcompress($data, 3);
            $status = $status + DATA_CACHE_COMPRESS; //标记数据压缩
        }
        $check = '';
        if ($this->_appHelper->config("DATA_CACHE_CHECK")) {//开启数据校验
            $check = md5($data);
            $status = $status + DATA_CACHE_CHECK;
        }

        $data = "<?php\n//" . sprintf('%012d', $arg_expire) . sprintf('%02d', $status) . $check . $data . "\n?>";
        $result = file_put_contents($filename, $data);

        if (!$result)
            return false;

        clearstatcache();
        return true;

    }


    public function delete($arg_key)
    {
        return unlink($this->_filename($arg_key));
    }

    public function update($arg_key, $arg_value, $arg_expire = null)
    {
        $filename = $this->_filename($arg_key);
        if (!is_file($filename)) {
            return false;
        }
        return $this->set($arg_key, $arg_value, $arg_expire);
    }

    /**
     * 清除缓存
     * @return bool
     */
    public function clear()
    {
        $path = $this->_options['temp'];
        $files = scandir($path);
        if (!$files)
            return false;
        foreach ($files as $file) {
            if (is_file($path . $file)) {
                unlink($path . $file);
            }
        }

        return true;
    }

    /**
     * @access protected
     * @return bool
     */
    protected function _enabled()
    {
        return true;
    }

    /**
     * @access public
     * @return bool
     */
    public function enabled()
    {
        return !empty($this->_options["temp"]);
    }


    /**
     * @return string
     */
    public function info()
    {
        return "File";
    }


}

