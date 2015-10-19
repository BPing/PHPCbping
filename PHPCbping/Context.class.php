<?php


/**
 * 上下文
 *
 * Class Context
 *
 * @author
 *
 */
class Context
{

    /**  @var MethodParams */
    private $_httpParams;


    public function __construct()
    {
        $this->_httpParams = \Utils\MethodParams::newInstance();
    }

    /**
     * @param $arg_output
     */
    public function err_echo($arg_output)
    {
        echo $arg_output;
    }

    /**
     * 输出字符串并结束请求
     *
     * @param  $arg_output string 输出内容
     */
    public function str_echo($arg_output)
    {
        echo $arg_output;
        exit;
    }

    /**
     * 把数组转成json字符串输出
     *
     * @notice 兼容字符串输出
     * @param array $arg_output 输出内容
     * @throws \Exceptions\EchoException
     * @see str_echo
     */
    public function json_echo($arg_output)
    {
        if (is_array($arg_output)) {
            $arg_output = json_encode($arg_output);
        }
        if (is_string($arg_output)) {
            $this->str_echo($arg_output);
        }
        throw new \Exceptions\EchoException("Output type exception");
    }

    /**
     * 魔术方法__call
     *
     * @param $method
     * @param $args
     * @return mixed|void
     * @throws Exception
     */
    public function __call($method, $args)
    {
        //调用http请求参数封装类类型自己的方法
        if (method_exists($this->_httpParams, $method)) {
            return call_user_func_array(array($this->_httpParams, $method), $args);
        } else {
            throw new Exception("the method don't exist");
        }
    }

}