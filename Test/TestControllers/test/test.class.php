<?php
/**
 * Created by PhpStorm.
 * User: bing
 * Date: 2015/9/3
 * Time: 19:46
 */

namespace TestControllers;


class test extends \Controller
{

    /**
     * 最终执行入口
     * @param $arg_context
     * @return mixed
     * @throws
     */
    function  doExecute(\Context $arg_context)
    {
        $exc = $arg_context->Params("exc");
        if ($exc == "echo_exc") {
            file_put_contents(dirname(dirname(dirname(__FILE__))) . "/temp/testForCmd.text", "echo_exc");
            $arg_context->json_echo(new test());
        }
        if ($exc == "exc") {
            file_put_contents(dirname(dirname(dirname(__FILE__))) . "/temp/testForCmd.text", "exc");
            throw new \Exception("test Exception");
        }
        file_put_contents(dirname(dirname(dirname(__FILE__))) . "/temp/testForCmd.text", "test.test");
    }

}