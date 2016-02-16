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
     * Sub-driver
     *
     * @used-by    PdoDriver
     * @var    string
     */
    public $subdriver;

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

    // --------------------------------------------------------------------

    public function __construct($params)
    {
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $this->$key = $val;
            }
        }
        log_message(LOG_INFO, 'Database Driver Class Initialized');
    }

    // --------------------------------------------------------------------

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
     * @return      mixed
     */
    public function reconnect()
    {
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * 关闭 DB 连接
     *
     * @return    void
     */
    public function close()
    {
        if ($this->conn_id) {
            $this->_close();
            $this->conn_id = FALSE;
        }
    }

    // --------------------------------------------------------------------

    /**
     * 关闭 DB 连接
     *
     * 此方法将被大部分drivers所覆盖。
     *
     * @return    void
     */
    protected function _close()
    {
        $this->conn_id = FALSE;
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

    // --------------------------------------------------------------------

    /**
     * @param $sql
     * @param bool|FALSE $binds
     */
    public function query($sql, $binds = FALSE)
    {

    }

    // --------------------------------------------------------------------

    /**
     * 执行sql语句
     * @param $sql
     */
    protected function _execute($sql)
    {
    }
    // --------------------------------------------------------------------
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

        //当事务被嵌套，我们只 begin/commit/rollback的最外面的
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

        //当事务被嵌套，我们只 begin/commit/rollback的最外面的
        if ($this->_trans_depth > 1) {
            $this->_trans_depth -= 1;
            return TRUE;
        } else {
            $this->_trans_depth = 0;
        }

        //query() 函数将在这个事件标志设置为false，一个查询失败
        if ($this->_trans_status === FALSE OR $this->_trans_failure === TRUE) {
            $this->trans_rollback();
            log_message('debug', 'DB Transaction Failure');
            return FALSE;
        }

        $this->trans_commit();
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * 事务开启
     *
     * @return    bool
     */
    abstract public function trans_begin();

    // --------------------------------------------------------------------

    /**
     * 事务提交
     *
     * @return    bool
     */
    abstract public function trans_commit();

    // --------------------------------------------------------------------

    /**
     * 事务回滚
     *
     * @return    bool
     */
    abstract public function trans_rollback();

    // --------------------------------------------------------------------

    /**
     *可以检索事务标志，以确定它是否已失败
     *
     * @return    bool
     */
    public function trans_status()
    {
        return $this->_trans_status;
    }

    // --------------------------------------------------------------------

    /**
     * @param $code
     * @param $msg
     * @param bool|true $throw
     * @throws Exception
     */
    protected function display_error($code, $msg, $throw = true)
    {
        log_message(LOG_ERR, 'DB  code:' . $code . ' message:' . $msg);
        if ($throw) {
            throw_e($msg, $code, 'DB');
        }
    }

}

