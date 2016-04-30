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

    /**
     * Bind marker
     *
     * Character used to identify values in a prepared statement.
     *
     * @var    string
     */
    public $bind_marker = '?';

    /**
     * Bind marker assoc
     *
     * Character used to identify values in a prepared statement.
     *
     * @var    string
     */
    public $bind_marker_assoc = ':';

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
     * 使用的平台名字(mysql, pdo, etc...)
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
     * 执行query
     *
     * @param $arg_sql
     * @param array|bool $arg_binds =FALSE
     * @param bool $arg_assoc =FALSE
     * @return  boolean|object
     */
    public function query($arg_sql, $arg_binds = FALSE, $arg_assoc = FALSE)
    {
        if ($arg_sql === '') {
            $this->display_error('', 'Invalid query: ' . $arg_sql, false);
            return FALSE;
        }

        //绑定变量处理
        if ($arg_binds !== FALSE) {
            $arg_sql = $this->compile_binds($arg_sql, $arg_binds, $arg_assoc);
        }

        //执行sql语句
        if (FALSE === ($this->result_id = $this->_query($arg_sql))) {
            //执行语句失败
            $error = $this->error_info();
            $this->display_error($error['code'], 'Query error: ' . $error['message'] . ' - Invalid query: ' . $arg_sql, false);
            return FALSE;
        }

        //写入语句则返回true
        if ($this->is_write_type($arg_sql)) return TRUE;

        //查询读取语句则返回对象数据
        //加载和实例化的结果集对象
        $driver = $this->load_db_result();
        $RES = new $driver($this);

        return $RES;
    }

    // --------------------------------------------------------------------

    /**
     * @param $arg_sql
     * @return    mixed
     */
    protected function _query($arg_sql)
    {
        if (!$this->conn_id) {
            $this->initialize();
        }
        return $this->_execute($arg_sql);
    }
    // --------------------------------------------------------------------
    /**
     * 执行sql语句
     * @param $arg_sql
     */
    abstract protected function _execute($arg_sql);

    // --------------------------------------------------------------------

    /**
     * 是否是写入语句
     * @param $arg_sql
     * @return bool
     */
    public function is_write_type($arg_sql)
    {
        return (bool)preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', $arg_sql);
    }

    // --------------------------------------------------------------------
    /**
     * 编译绑定变量
     *
     * @param    string $arg_sql sql语句
     * @param    array $arg_binds 绑定数据的数组
     * @param    bool $arg_assoc =FALSE
     *               采用关联。
     *               $this->bind_marker_assoc=':'时，
     *
     *               如：a=:a and b=:b   $arg_binds=array('a'=>'1','b'=>'2')。
     *
     *               务必确保绑定变量不存在字符串当中，
     *
     *               如 a=':a' and b=:b $arg_binds=array('a'=>'1','b'=>'2'),
     *
     *               但你可以如此般处理：
     *
     *               如 a=':a' and b=:b $arg_binds=array('b'=>'2'),这就不存在:a替换了。
     *
     *               还有，此方法无法保证是否所有绑定变量都替换了，也无法确定你给的$arg_binds是否符合数量要求。
     *
     *               所以，慎用！
     *
     * @return    string
     */
    public function compile_binds($arg_sql, $arg_binds, $arg_assoc = FALSE)
    {
        if (empty($arg_binds)) return $arg_sql;

        if (!$arg_assoc) {

            if (empty($arg_binds) OR empty($this->bind_marker) OR strpos($arg_sql, $this->bind_marker) === FALSE) {
                return $arg_sql;
            }
            if (!is_array($arg_binds)) {
                $binds = array($arg_binds);
                $bind_count = 1;
            } else {
                //构建数字索引数组
                $binds = array_values($arg_binds);
                $bind_count = count($binds);
            }

            //我们将需要标记长度
            $ml = strlen($this->bind_marker);

            // 确保字符串内的标记不被替换
            if ($c = preg_match_all("/'[^']*'/i", $arg_sql, $matches)) {
                $c = preg_match_all('/' . preg_quote($this->bind_marker, '/') . '/i',
                    str_replace($matches[0],
                        str_replace($this->bind_marker, str_repeat(' ', $ml), $matches[0]),
                        $arg_sql, $c),
                    $matches, PREG_OFFSET_CAPTURE);

                // 绑定值数量必须和query句子中的标记的数量相匹配
                if ($bind_count !== $c) {
                    return $arg_sql;
                }
            } elseif (($c = preg_match_all('/' . preg_quote($this->bind_marker, '/') . '/i', $arg_sql, $matches, PREG_OFFSET_CAPTURE)) !== $bind_count) {
                return $arg_sql;
            }

            //循环把标记替换成绑定值
            do {
                $c--;
                $escaped_value = $this->escape($binds[$c]);
                if (is_array($escaped_value)) {
                    $escaped_value = '(' . implode(',', $escaped_value) . ')';
                }
                $arg_sql = substr_replace($arg_sql, $escaped_value, $matches[0][$c][1], $ml);
            } while ($c !== 0);

            return $arg_sql;

        } else {//关联数组型处理
            if (!is_array($arg_binds))
                return $arg_sql;
            $patten = array();
            $replace = array();
            foreach ($arg_binds as $key => $val) {
                $patten[] = $this->bind_marker_assoc . $key;
                $replace[] = $this->escape($val);
            }
            $arg_sql = preg_replace($patten, $replace, $arg_sql);

            return $arg_sql;
        }

    }

// --------------------------------------------------------------------

    /**
     * 转义字符串
     *
     * @param    string
     * @return    mixed
     */
    public function escape($str)
    {
        if (is_array($str)) {

            $str = array_map(array(&$this, 'escape'), $str);
            return $str;

        } elseif (is_string($str) OR (is_object($str) && method_exists($str, '__toString'))) {

            return "'" . str_replace("'", "''", remove_invisible_characters($str)) . "'";

        } elseif (is_bool($str)) {

            return ($str === FALSE) ? 0 : 1;

        } elseif ($str === NULL) {

            return 'NULL';
        }

        return $str;
    }

    // --------------------------------------------------------------------

    /**
     * 加载对应的结果集对象
     *
     * @return    string    结果集对象名字
     */
    public function load_db_result()
    {
        $driver = ucfirst(strtolower($this->dbdriver)) . 'Result';
        return $driver;
    }

    // --------------------------------------------------------------------
    /**
     * 开始事务
     *
     * @param    bool $arg_test_mode = FALSE
     * @return    void
     */
    public function trans_start($arg_test_mode = FALSE)
    {
        if (!$this->trans_enabled) {
            return;
        }

        //当事务被嵌套，我们只 begin/commit/rollback的最外面的
        if ($this->_trans_depth > 0) {
            $this->_trans_depth += 1;
            return;
        }

        $this->trans_begin($arg_test_mode);
        $this->_trans_depth += 1;
    }

    // --------------------------------------------------------------------

    /**
     * 完成事务
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
     * @param    bool $arg_test_mode
     * @return    bool
     */
    abstract public function trans_begin($arg_test_mode);

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
     * Error
     * 返回包含最后一次数据库错误代码和消息的数组。
     *  array('code'=>'000000','message'=>'')
     */
    abstract public function error_info();

    // --------------------------------------------------------------------

    /**
     * @param $arg_code
     * @param $arg_msg
     * @param bool|true $arg_throw
     * @throws Exception
     */
    protected function display_error($arg_code, $arg_msg, $arg_throw = true)
    {
        log_message(LOG_ERR, 'DB  code:' . $arg_code . ' message:' . $arg_msg);
        if ($arg_throw) {
            throw_e($arg_msg, $arg_code, 'DB');
        }
    }

}

