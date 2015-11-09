<?php

namespace Controllers;

class HelloWorld extends \Controller
{
    /**
     * 最终执行入口
     * @param $arg_context
     */
    function  doExecute(\Context $arg_context)
    {
        $arg_context->json_echo(array("hello" => "hello world"));
     //   trigger_error("hello world", E_USER_ERROR);
    }

}