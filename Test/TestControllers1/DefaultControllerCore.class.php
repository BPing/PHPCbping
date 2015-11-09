<?php
namespace TestControllers1;

/**
 * 默认控制器
 *
 * Class DefaultController
 * @package Controllers
 */
class DefaultControllerCore extends \Controller
{

    /**
     * 最终执行入口
     * @param $arg_context
     * @return mixed
     */
    function  doExecute(\Context $arg_context)
    {
        file_put_contents(dirname(dirname(__FILE__)) . "/temp/testForCmd.text", "default.default");
    }


}