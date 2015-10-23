<?php
require_once "define.php";
require_once "AppHelper.class.php";
require_once 'Log.interface.php';
require_once 'Utils\Logs.class.php';
require_once 'Utils\UtilFactory.class.php';


class LogsTest extends PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        \Utils\UtilFactory::getLogHandle()->logInfo("nihao");
        \Utils\UtilFactory::getLogHandle()->logDebug("nihao");
        \Utils\UtilFactory::getLogHandle()->logError("nihao");
        \Utils\UtilFactory::getLogHandle()->logWarn("nihao");

        if (\Utils\UtilFactory::getLogHandle()->getError() != '') {
            $this->assertTrue(false, "log Exception");
        }

    }

    public function testLogError01()
    {
        \Utils\Logs::getInstance()->init(BASEPATH . 'fog////');
        $this->assertTrue(\Utils\UtilFactory::getLogHandle()->getError() != '', "init error");
    }

    public function testLogError02()
    {
        \Utils\UtilFactory::getLogHandle()->logTest('test');
        $this->assertTrue(\Utils\UtilFactory::getLogHandle()->getError() == LOG_TYPE_ERROR, "log error");
    }

    public function testLogError03()
    {
        \Utils\Logs::getInstance()->init(array());
        $this->assertTrue(\Utils\UtilFactory::getLogHandle()->getError() == NOT_FILE_ERROR, NOT_FILE_ERROR . ":init error");
    }


}
