<?php
namespace Utils;

define("OPEN_LOGFILE_ERROR", "打开日志文件失败");
define("NOT_FILE_ERROR", "此路径不是文件路径");
define("LOG_TYPE_ERROR", "日志级别错误。非 Error Warn Info Debug");

/**
 * Class Logs
 * 日志处理类
 *   统一使用本类进行日志记录操作
 *   支持日志级别：Error Warn Info Debug
 *
 * @date 2015.06.17
 * @author cbping
 * @package Utils
 */
class Logs implements \LogI
{

    /**单例模式 @var Logs */
    private static $instance_;

    /**日志文件 @var string */
    private static $logFile_;

    /**文件句柄 @var resource */
    private static $fileHandle_ = null;

//    /**日志前面信息 @var string */
//    private static $before_;

    /**错误信息记录 @var string */
    private $errMsg_ = '';

    const debug = 'Debug';
    const warn = 'Warn';
    const info = 'Info';
    const error = 'Error';

    private static $type_ = array(self::debug, self:: warn, self:: info, self:: error);


    private function __construct()
    {

    }

    /**析构函数 */
    function __destruct()
    {
        if (!is_null(self::$fileHandle_)) {
            fclose(self::$fileHandle_);
            self::$fileHandle_ = null;
        }
    }

    /**
     * 返回实例
     *
     * @return Logs
     */
    public static function getInstance()
    {
        if (empty(self::$instance_))
            self::$instance_ = new Logs();

        return self::$instance_;
    }

    /**
     * @param null $arg_file string
     * @throws
     * @return bool
     */
    public function init($arg_file = null)
    {
        if (null !== $arg_file)
            $this->setLogFile($arg_file);

        if (!is_string(self::$logFile_)) {
            $this->_setError(NOT_FILE_ERROR);
            return false;
        }
        try {
            //目录不存在则创建
            is_readable(dirname(self::$logFile_)) or mkdir(dirname(self::$logFile_), 0700, true);
            //读写方式打开，将文件指针指向文件末尾。如果文件不存在则尝试创建之。
            self::$fileHandle_ = fopen(self::$logFile_, 'a+');

            if (!is_resource(self::$fileHandle_)) {
                throw new Exception(OPEN_LOGFILE_ERROR);
            }

        } catch (\Exception $e) {
            $this->_setError($e->getMessage());
            self::$fileHandle_ = null;
            return false;
        }

        return true;
    }

    /**
     * @param $arg_err string
     */
    private function _setError($arg_err)
    {
        $this->errMsg_ = $arg_err;
    }


    /**
     * @param $arg_file string
     */
    public static function setLogFile($arg_file)
    {
        self::$logFile_ = $arg_file;
    }

    /**
     * @param $arg_msg
     * @see _logMessage
     */
    public function logError($arg_msg)
    {
        $this->_logMessage($arg_msg, self::error);
    }

    /**
     * @param $arg_msg
     * @see _logMessage
     */
    public function logDebug($arg_msg)
    {
        $this->_logMessage($arg_msg, self::debug);
    }

    /**
     * @param $arg_msg
     * @see _logMessage
     */
    public function logInfo($arg_msg)
    {
        $this->_logMessage($arg_msg, self::info);
    }

    /**
     * @param $arg_msg
     * @see _logMessage
     */
    public function logWarn($arg_msg)
    {
        $this->_logMessage($arg_msg, self::warn);
    }

    /**
     * 日志记录
     *
     * @param $arg_msg
     *        日志需要记录的信息
     * @param $arg_type string
     *        日志级别
     * @see $type_
     */
    private function _logMessage($arg_msg, $arg_type)
    {
        if (is_null(self::$fileHandle_))
            return;
        if (!in_array($arg_type, self::$type_)) {
            $this->_setError(LOG_TYPE_ERROR);
            return;
        }
        $strMsg = print_r($arg_msg, true); //信息转化成字符串形式
        $strMsg = '[' . date("Y/m/d h:i:s", time()) . '][' . $arg_type . ']:' . $strMsg . "\n";
        fwrite(self::$fileHandle_, $strMsg);//把信息写入文件中

    }

    public function getError()
    {
        return $this->errMsg_;
    }

    /**
     *  for test
     * @param $arg_msg
     */
    public function logTest($arg_msg)
    {
        $this->_logMessage($arg_msg, 'ddd');

    }
}


