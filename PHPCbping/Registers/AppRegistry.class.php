<?php

namespace Registers;

/**
 * 应用程序注册类  Class AppRegister
 *
 *
 * @package Registers
 * @author cbping
 */
class AppRegistry
{
    /** @var AppRegistry */
    private static $_instance;

    /** 保存数据驱动 @var driver */
    private $_driver;


    private function __construct()
    {
    }

    /**
     * @return AppRegistry
     */
    public function getInstance($arg_type = '', $arg_options = array())
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
            self::$_instance->init($arg_type, $arg_options);
        }
        return self::$_instance;
    }

    /**
     * 初始化工作
     *
     */
    public function init($arg_type = '', $arg_options = array())
    {
        $this->_driver = Driver::getInstance($arg_type, $arg_options);
    }

    /**
     * 获取值
     *
     * @param $arg_key string
     * @return mixed
     */
    public function  get($arg_key)
    {
        return $this->_driver->get($arg_key);
    }

    /**
     * 设置值（注册）
     *
     * @param $arg_key string
     * @param $arg_value mixed
     * @param $arg_expire int 有效时间（单位S）
     * @return mixed
     */
    public function  set($arg_key, $arg_value, $arg_expire = null)
    {
        return $this->_driver->set($arg_key, $arg_value, $arg_expire);
    }

    /**
     * 清除注册表
     *
     * @return mixed
     */
    public function clear()
    {
        return $this->_driver->clear();

    }

    /**
     * 删除注册表
     *
     * @param $arg_key 需要删除的键名
     * @return mixed
     */
    public function delete($arg_key)
    {
        return $this->_driver->delete($arg_key);
    }

    /**
     * 更新注册表的值
     *
     * @param $arg_key 需要更新的键名
     * @param $arg_value 更新后的值
     * @param $arg_expire int 有效时间（S）
     * @return mixed
     */
    public function update($arg_key, $arg_value, $arg_expire = null)
    {
        return $this->_driver->update($arg_key, $arg_value, $arg_expire);
    }

    /**
     * 魔术方法__call
     *
     * @param $method
     * @param $args
     * @return mixed|void
     */
    public function __call($method, $args)
    {
        //调用缓存类型自己的方法
        if (method_exists($this->_driver, $method)) {
            return call_user_func_array(array($this->_driver, $method), $args);
        } else {
            //TODO:异常处理
            return;
        }
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->set($name, $value);
    }
}