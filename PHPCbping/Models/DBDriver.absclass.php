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

    /**
     * Result ID
     *
     * @var    object|resource
     */
    public $result_id = FALSE;

    /**
     * Character set
     *
     * @var    string
     */
    public $char_set = 'utf8';

    /**
     * Transaction enabled flag
     *
     * @var    bool
     */
    public $trans_enabled = TRUE;

    /**
     * Transaction depth level
     *
     * @var    int
     */
    protected $_trans_depth = 0;

    /**
     * Transaction status flag
     *
     * Used with transactions to determine if a rollback should occur.
     *
     * @var    bool
     */
    protected $_trans_status = TRUE;

    /**
     * Transaction failure flag
     *
     * Used with transactions to determine if a transaction has failed.
     *
     * @var    bool
     */
    protected $_trans_failure = FALSE;


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
     * 数据库初始化
     */
    public function initialize()
    {
        if ($this->conn_id) {
            return TRUE;
        }
        $this->conn_id = $this->db_connect($this->pconnect);

        if (!$this->conn_id) {
            $this->display_error(CONN_FAIL, 'db connect fail');
        }
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
     * @return    string
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
     * @return    string
     */
    protected function _version()
    {
        return 'SELECT VERSION() AS ver';
    }


    public function query($sql, $binds = FALSE)
    {

    }

    /**
     * Start Transaction
     *
     * @param    bool $test_mode = FALSE
     * @return    void
     */
    public function trans_start($test_mode = FALSE)
    {
        if (!$this->trans_enabled) {
            return;
        }

        // When transactions are nested we only begin/commit/rollback the outermost ones
        if ($this->_trans_depth > 0) {
            $this->_trans_depth += 1;
            return;
        }

        $this->trans_begin($test_mode);
        $this->_trans_depth += 1;
    }

    // --------------------------------------------------------------------

    /**
     * Complete Transaction
     *
     * @return    bool
     */
    public function trans_complete()
    {
        if (!$this->trans_enabled) {
            return FALSE;
        }

        // When transactions are nested we only begin/commit/rollback the outermost ones
        if ($this->_trans_depth > 1) {
            $this->_trans_depth -= 1;
            return TRUE;
        } else {
            $this->_trans_depth = 0;
        }

        // The query() function will set this flag to FALSE in the event that a query failed
        if ($this->_trans_status === FALSE OR $this->_trans_failure === TRUE) {
            $this->trans_rollback();

//            // If we are NOT running in strict mode, we will reset
//            // the _trans_status flag so that subsequent groups of transactions
//            // will be permitted.
//            if ($this->trans_strict === FALSE) {
//                $this->_trans_status = TRUE;
//            }

            log_message('debug', 'DB Transaction Failure');
            return FALSE;
        }

        $this->trans_commit();
        return TRUE;
    }



    // --------------------------------------------------------------------

    /**
     * Begin Transaction
     *
     * @return    bool
     */
    abstract public function trans_begin();


    // --------------------------------------------------------------------

    /**
     * Commit Transaction
     *
     * @return    bool
     */
    abstract public function trans_commit();


    // --------------------------------------------------------------------

    /**
     * Rollback Transaction
     *
     * @return    bool
     */
    abstract public function trans_rollback();


    // --------------------------------------------------------------------

    /**
     * Lets you retrieve the transaction flag to determine if it has failed
     *
     * @return    bool
     */
    public function trans_status()
    {
        return $this->_trans_status;
    }

    /**
     * @param $code
     * @param $msg
     * @param bool|true $throw
     * @throws Exception
     */
    protected function display_error($code, $msg, $throw = true)
    {
        if ($throw) {
            throw_e($msg, $code, 'DB');
        }
    }

}

