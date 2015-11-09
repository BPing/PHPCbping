<?php

/**
 * 日志接口
 *   支持日志级别：Error Warn Info Debug
 * Class LogI
 */
interface LogI
{
    /**
     * @param $arg_msg
     * @flag Error
     */
    public function logError($arg_msg);

    /**
     * @param $arg_msg
     * @flag Debug
     */
    public function logDebug($arg_msg);


    /**
     * @param $arg_msg
     * @flag Info
     */
    public function logInfo($arg_msg);

    /**
     * @param $arg_msg
     * @flag Warn
     */
    public function logWarn($arg_msg);


}