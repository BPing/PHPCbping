<?php
namespace Utils;

class UtilFactory
{

    public static function getLogHandle()
    {
        $log=Logs::getInstance();
        $log->init(__DIR__."\\log.log");
        $log->logInfo("返回日志句柄");
        return $log;
    }
}