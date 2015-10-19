<?php

/**
 * 命令处理器。（Class CommandHandler）
 *
 * @author cbping
 * @see self::run  命令模式入口函数
 */
class CommandHandler
{

    private function __construct()
    {
    }

    /**
     * 初始化
     */
    function init()
    {


    }

    /**
     * 命令模式入口函数
     */
    static function  run()
    {
        $instance = new CommandHandler();
        $instance->init();
        $instance->_handleRequest();
    }

    /**
     * 处理请求
     *
     */
    private function  _handleRequest()
    {
        try {
            $context = new Context();
            $ctrl_r = new ControlResolver();
            $ctrl = $ctrl_r->getController($context);
            $ctrl->execute($context);
        } catch (\Exceptions\ResolverException $e) {

            $context->err_echo("ResolverException:" . $e->getMessage());
        } catch (\Exceptions\EchoException $e) {

            $context->err_echo("EchoException:" . $e->getMessage());
        } catch (Exception $e) {

            $context->err_echo("Exception:" . $e->getMessage());
        }

    }
}