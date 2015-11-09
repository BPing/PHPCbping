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
            log_message(LOG_ERR, "ResolverException:" . $e->getMessage());
            $context->err_echo("the Server Exception");
        } catch (\Exceptions\EchoException $e) {
            log_message(LOG_ERR, "EchoException:" . $e->getMessage());
            $context->err_echo("the Server Exception");
        } catch (Exception $e) {
            log_message(LOG_ERR, "Exception:" . $e->getMessage());
            $context->err_echo("the Server Exception");

        }

    }
}