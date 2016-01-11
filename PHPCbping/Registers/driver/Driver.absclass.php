<?php

namespace Registers;
use Exceptions\RegistersDriverException;

/**
 * Class driver
 * 所有注册器缓存驱动的基类
 * 此抽象类提供静态办法获取相应缓存驱动实例，
 * 将被注册器内部调用
 * @require AppHelper
 * @abstract
 * @author cbping
 */
abstract class Driver
{

    /** @var array  缓存参数 */
    protected $_options = array();

    protected $_appHelper;

    /**
     * 架构函数
     * @param array $arg_options 缓存参数
     * @access public
     * @throws
     */
    public function __construct($arg_options = array())
    {
        if (!$this->_enabled())
            throw new \Exception("no support:" . $this->info());
        $this->_appHelper = \AppHelper::Instance();
        $this->_options = $arg_options;
    }

    /**
     * 读取缓存
     * @access public
     * @param string $arg_key 缓存变量名
     * @return mixed
     */
    abstract public function  get($arg_key);

    /**
     * 写入缓存
     * @access public
     * @param string $arg_key 缓存变量名
     * @param mixed $arg_value 存储数据
     * @param int $arg_expire 有效时间 0为永久
     * @return boolean
     */
    abstract public function  set($arg_key, $arg_value, $arg_expire = null);

    /**
     * 删除缓存
     * @access public
     * @param string $arg_key 缓存变量名
     * @return boolean
     */
    abstract public function delete($arg_key);


    /**
     * @param $arg_key
     * @param $arg_value
     * @param null $arg_expire
     * @return mixed
     */
    abstract public function update($arg_key, $arg_value, $arg_expire = null);

    /**
     * 清除缓存
     * @access public
     * @return boolean
     */
    abstract public function clear();

    /**
     * 是否可用
     * @return bool
     */
    abstract protected function _enabled();


    /**
     * 返回缓存驱动的标识则名字
     */
    abstract public function info();


    /**
     * 获取缓存驱动类
     *
     * @param int $arg_type
     * @param array $arg_options
     * @return driver
     * @throws RegistersDriverException
     */
    public static function getInstance($arg_type = FILE_D, $arg_options = array())
    {
        switch ($arg_type) {
            case APC_D:
                return new Apc_d($arg_options);
                break;
            case FILE_D:
                return new File_d($arg_options);
                break;
            default:
                throw new RegistersDriverException('the type doesn\'t exist');
        }
    }

}

defined("APC_D") or define("APC_D", 1);
defined("FILE_D") or define("FILE_D", 2);