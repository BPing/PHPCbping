<?php
namespace Utils;

class UtilFactory
{

    public static function getLogHandle()
    {
        $log = Logs::getInstance();
        $logPath = \AppHelper::Instance()->config("LOG_PATH");
        $log->init(BASEPATH . $logPath . "log.log");
        $log->logInfo("返回日志句柄");
        return $log;
    }
}