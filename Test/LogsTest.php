<?php
require_once 'Log.interface.php';
require_once 'Utils\Logs.class.php';
require_once 'Utils\UtilFactory.class.php';

class LogsTest extends PHPUnit_Framework_TestCase
{
    public function testLog()
    {
        \Utils\UtilFactory::getLogHandle()->logInfo("nihao");
    }
}
