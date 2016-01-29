<?php

namespace Utils;

//公有
define("METHOD_GET", INPUT_GET);
define("METHOD_POST", INPUT_POST);

//本类独有
define("METHOD_ALL", 100); //包括get和post

define ('METHOD_COOKIE', INPUT_COOKIE);
define ('METHOD_ENV', INPUT_ENV);
define ('METHOD_SERVER', INPUT_SERVER);
define ('METHOD_SESSION', INPUT_SESSION);
define ('METHOD_REQUEST', INPUT_REQUEST);


/**错误信息 @var string */
define("KEY_TYPE_ERROR", "索引名字异常，非字符串类型 ");
define("CHECK_KEY_TYPE_ERROR", "CHECK索引名字异常，非字符串类型 ");
define("CHECK_CALLORREG_TYPR_ERROR", "CHECK不是回调函数或者正则表达式 ");
define("CHECK_IS_ERROR", "校验失败 ");
define("METHOD_KEY_TYPE_ERROR", "方法参数异常，非字符串类型 ");
define("METHOD_TYPE_ERROR", "非正常方法名，请重新检查 ");
define("FILTER_ERROR", "内置过滤器过滤异常 ");


/**
 * 获取http请求参数封装类
 *
 * 无法从外部来创建本类实例，可以通过对外接口静态方法newInstance()来创建本类实
 * @author cbping
 * @todo 没有转义处理哟
 */
class MethodParams
{
    /*** @var string 错误信息,最近一次的错误信息 */
    private $errMsg_ = "";

    /*** @var bool 错误标志,最近一次的错误标志 */
    private $errFlat_ = false;

    private function __construct()
    {
    }

    /**
     * 创建实例
     *
     * @return MethodParams
     */
    public static function newInstance()
    {
        return new MethodParams();
    }

    private function _getParams($arg_name)
    {
        return isset($_GET[$arg_name]) ? $_GET[$arg_name] : null;
    }

    private function _postParams($arg_name)
    {
        return isset($_POST[$arg_name]) ? $_POST[$arg_name] : null;
    }

    /**
     * @param $arg_method
     * @param $arg_name
     * @return null
     */
    private function _getValue($arg_method, $arg_name)
    { //XXX:其他方法未加
        $ostr = null;
        switch ($arg_method) {
            case METHOD_GET:
                $ostr = $this->_getParams($arg_name);
                break;

            case METHOD_POST:
                $ostr = $this->_postParams($arg_name);
                break;

            case METHOD_ALL:   //TODO $_REQUEST
                $ostr = $this->_getParams($arg_name);
                if (null === $ostr)
                    $ostr = $this->_postParams($arg_name);
                break;

            default:
                $this->_setError(METHOD_TYPE_ERROR);
                break;
        }

        return $ostr;
    }


    /**
     * 正则匹配
     *    只能匹配一个成功，否则为失败
     *
     * @param string $arg_string 输入字符串
     * @param string $arg_reg 匹配模式
     * @return bool 是否匹配成功
     */
    private function _ereg($arg_string, $arg_reg)
    {
        if (preg_match_all($arg_reg, $arg_string) === 1)
            return true;
        return false;

    }

    /**
     * @param  $arg_name string
     *         需要获取的http请求内容变量值的变量名字
     *
     * @param null $arg_check
     *        校验参数方法 如果为null的话代表不用校验
     *        包括回调函数校验或者正则校验
     *
     * @param $arg_method string default METHOD_ALL
     *        请求方法，根据方法不同从不同的超级全局变量中获取参数值
     *        如果不限定方法，则是首先考虑GET然后再是POST
     *
     * @return null 或者返回对应的值
     */
    private function _mainRun($arg_name, $arg_check = null, $arg_method = METHOD_ALL)
    {
        $ostr = null;
        do {
            if (!is_string($arg_name)) {
                $this->_setError(KEY_TYPE_ERROR);
                break;
            }

            if (!is_int($arg_method)) {
                $this->_setError(METHOD_KEY_TYPE_ERROR);
                break;
            }

            $ostr = $this->_getValue($arg_method, $arg_name);

            //不需要校验参数
            if (null === $arg_check || null === $ostr)
                break;

            if (is_callable($arg_check)) {    //回调函数处理分支
                if (call_user_func($arg_check, $ostr))
                    break;

            } elseif (is_string($arg_check)) {   //如果不是回调函数，则尝试正则校验
                $begin = strpos($arg_check, '/');
                $end = strrpos($arg_check, '/');
                $len = strlen($arg_check);
                //是否是'/XXX/'的字符串，X代表任意符号
                if (!($end - $begin == $len - 1)) {
                    $this->_setError(CHECK_CALLORREG_TYPR_ERROR);
                    break;
                }
                //进行正则校验
                if ($this->_ereg($ostr, $arg_check))
                    break;
            }

            //参数检查失败
            $this->_setError(CHECK_IS_ERROR . CHECK_CALLORREG_TYPR_ERROR);
            break;

        } while (false);

        if ($this->isErr())
            $ostr = null;

        return $ostr;
    }

    /**
     * 清理上一次的错误标志和信息，相当恢复到错误的初始状态
     */
    private function _cleanError()
    {
        $this->errFlat_ = false;
        $this->errMsg_ = "";
    }

    /**
     * 设置错误标志和错误信息
     * @param $arg_errMsg stirng 错误信息
     */
    private function _setError($arg_errMsg)
    {
        $this->errFlat_ = true;
        $this->errMsg_ = $arg_errMsg;
    }


    /**
     * 获取POST方法http请求的参数内容
     *
     * @param $arg_name string
     *          参数名
     * @param $arg_default mixed
     *         默认值 当值不存在或者值不符合要求时，返回默认值
     * @param $arg_check string
     *        校验方法
     * @return 默认值或者参数值
     * @see Params
     */
    public function PostParams($arg_name, $arg_default, $arg_check)
    {
        return $this->Params($arg_name, $arg_default, $arg_check, METHOD_POST);
    }


    /**
     * 获取GET方法http请求的参数内容
     *
     * @param $arg_name string
     *          参数名
     * @param $arg_default mixed
     *         默认值 当值不存在或者值不符合要求时，返回默认值
     * @param $arg_check
     *        校验方法
     * @return 默认值或者参数值
     * @see Params
     */
    public function GetParams($arg_name, $arg_default, $arg_check)
    {
        return $this->Params($arg_name, $arg_default, $arg_check, METHOD_GET);
    }

    /**
     * 获取http请求中参数的值
     *
     * @param $arg_name
     * @param $arg_default
     * @param $arg_check
     * @param string $arg_method default METHOD_ALL
     *        请求方法，根据方法不同从不同的超级全局变量中获取参数值
     *        如果不限定方法，则是首先考虑GET然后再是POST
     * @return null
     * @see PostParams
     * @see GetParams
     */
    public function Params($arg_name, $arg_default = null, $arg_check = null, $arg_method = METHOD_ALL)
    {
        $this->_cleanError();
        $ostr = $this->_mainRun($arg_name, $arg_check, $arg_method);
        if (null === $ostr)
            return $arg_default;
        return $ostr;
    }

    /***
     * 采用内置过滤器获过滤输入的数据
     *
     * @param $arg_type
     *           方法类型
     * @param $arg_variable_name
     * @param $arg_default
     *          默认值 当值不存在或者值不符合要求时，返回默认值
     * @param int $arg_filter
     * @param null $arg_options
     * @return mixed
     * @see filter_var
     */
    public function FilterInput($arg_type, $arg_variable_name, $arg_default, $arg_filter = FILTER_DEFAULT, $arg_options = null)
    {
        $this->_cleanError();
        $ostr = $this->_mainRun($arg_variable_name, null, $arg_type);
        if (null === $ostr) {
            return $arg_default;
        }

        $res = filter_var($ostr, $arg_filter, $arg_options);
        if (FALSE === $res || null === $res) {
            $this->_setError(FILTER_ERROR);
            return $arg_default;
        }
        return $res;
    }

    public function isErr()
    {
        return $this->errFlat_;
    }

    public function getError()
    {
        return $this->errMsg_;
    }


}




