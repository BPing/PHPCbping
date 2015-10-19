<?php

/**
 * 所有控制类的基类
 *
 * Class Controller
 */
abstract class Controller
{
    /**
     * 采用 final限制，统一构造方法
     */
    final function __construct()
    {
    }

    /**
     * 执行入口（对外）
     * @param $arg_context
     */
    function execute(Context $arg_context)
    {
        $this->doExecute($arg_context);
    }

    /**
     * 最终执行入口
     * @param $arg_context
     */
    abstract function  doExecute(Context $arg_context);
}

