<?php

/**
 *
 * 数据库抽象基类
 *
 * @author cbping
 * Class DBDriver
 */
abstract class DBDriver
{

    /**
     * Data Source Name / Connect string
     *
     * @var    string
     */
    public $dsn;

    /**
     * Username
     *
     * @var    string
     */
    public $username;

    /**
     * Password
     *
     * @var    string
     */
    public $password;

    /**
     * Hostname
     *
     * @var    string
     */
    public $hostname;

    /**
     * Database name
     *
     * @var    string
     */
    public $database;

    /**
     * Database driver
     *
     * @var    string
     */
    public $dbdriver = 'mysqli';

    /**
     * Persistent connection flag
     *
     * @var    bool
     */
    public $pconnect = FALSE;

    /**
     * Connection ID
     *
     * @var    object|resource
     */
    public $conn_id = FALSE;


    public function __construct($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $this->$key = $val;
            }
        }
        log_message(LOG_INFO, 'Database Driver Class Initialized');
    }


    /**
     * DB连接
     *
     * 这只是一个虚拟的方法，所有的drivers都会重写它。.
     *
     * @return      mixed
     */
    public function db_connect()
    {
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 数据库持久连接
     *
     * @return    mixed
     */
    public function db_pconnect()
    {
        return $this->db_connect(TRUE);
    }

    // --------------------------------------------------------------------

    /**
     *重新连接
     *
     *保持/恢复数据库连接如果没有查询已
     *发送超过服务器的空闲超时时间的长度。
     *
     *这是一个虚拟的方法，让驱动没有这样的
     *功能不声明它，而其他人将重写它。
     * @return      void
     */
    public function reconnect()
    {
    }

    public function close()
    {

    }

    // --------------------------------------------------------------------

    /**
     * 使用的平台名字(mysql, mssql, etc...)
     *
     * @return    string
     */
    public function platform()
    {
        return $this->dbdriver;
    }

    // --------------------------------------------------------------------

    /**
     * Database version number
     *
     * Returns a string containing the version of the database being used.
     * Most drivers will override this method.
     *
     * @return	string
     */
    public function version()
    {
//        if (isset($this->data_cache['version']))
//        {
//            return $this->data_cache['version'];
//        }
//
//        if (FALSE === ($sql = $this->_version()))
//        {
//            return ($this->db_debug) ? $this->display_error('db_unsupported_function') : FALSE;
//        }
//
//        $query = $this->query($sql)->row();
//        return $this->data_cache['version'] = $query->ver;
    }

    // --------------------------------------------------------------------

    /**
     * Version number query string
     *
     * @return	string
     */
    protected function _version()
    {
        return 'SELECT VERSION() AS ver';
    }


    public function query($sql, $binds = FALSE){

    }

}

