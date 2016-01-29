<?php
namespace Registers;

/**
 * Interface Registry
 *
 * @package Registers
 * @author cbping
 * @deprecated 
 */
interface Registry
{

    /**
     * 获取值
     *
     * @param $arg_key string
     * @return mixed
     */
    public function  get($arg_key);

    /**
     * 设置值（注册）
     *
     * @param $arg_key string
     * @param $arg_value mixed
     * @return mixed
     */
    public function  set($arg_key, $arg_value);


//    public function isEmpty(){
//
//    }
    /**
     * 删除注册表
     *
     * @param $arg_key 需要删除的键名
     * @return mixed
     */
    public function delete($arg_key);

    /**
     * 更新注册表的值
     *
     * @param $arg_key 需要更新的键名
     * @param $arg_value 更新后的值
     * @return mixed
     */
    public function update($arg_key, $arg_value);

    /**
     * 清除注册表
     *
     * @return mixed
     */
    public function clear();


}