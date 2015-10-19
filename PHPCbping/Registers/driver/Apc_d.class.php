<?php

namespace Registers;

require "Driver.absclass.php";

/**
 * Class Apc_d
 * @package Registers
 * @author cbping
 * @see Driver
 */
class Apc_d extends Driver
{

    public function  get($arg_key)
    {
        return apc_fetch($arg_key);
    }

    public function  set($arg_key, $arg_value, $arg_expire = null)
    {
        return apc_store($arg_key, $arg_value);
    }

    public function delete($arg_key)
    {
        return apc_delete($arg_key);
    }

    public function update($arg_key, $arg_value, $arg_expire = null)
    {
        if (apc_exists($arg_key))
            return apc_store($arg_key, $arg_value);
        return false;
    }

    public function clear()
    {
        return apc_clear_cache();
    }

    /**
     * @return bool
     */
    protected function _enabled()
    {
        return (extension_loaded('apc') && ini_get('apc.enabled')) ? true : false;
    }

    public function info()
    {
        return "Apc";
    }
}
