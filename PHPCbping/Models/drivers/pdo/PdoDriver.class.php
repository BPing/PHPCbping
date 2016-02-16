<?php

/**
 * PDO
 * Class PdoDriver
 * @author cbping
 */
class PdoDriver extends DBDriver
{

    /**
     * PDO Options
     *
     * @var    array
     */
    public $options = array();


    public function __construct($params)
    {
        parent::__construct($params);
        $this->dbdriver = 'pdo';
        if (preg_match('/([^:]+):/', $this->dsn, $match) && count($match) === 2) {
            //如果有一个最小的有效DSN字符串模式中，我们就大功告成了
            //这是一般的PDO的用户，有完整的DSN字符串。
            $this->subdriver = strtolower($match[1]);

        } //支持在主机名称字段中指定DSN
        elseif (preg_match('/([^:]+):/', $this->hostname, $match) && count($match) === 2) {
            $this->dsn = $this->hostname;
            $this->hostname = NULL;
            $this->subdriver = strtolower($match[1]);

        } //DSN异常
        else {
            $this->display_error('', 'PDO: Invalid dsn', false);
            return;
        }
        if (!in_array($this->subdriver, array('4d', 'cubrid', 'dblib', 'firebird', 'ibm', 'informix', 'mysql', 'oci', 'odbc', 'pgsql', 'sqlite', 'sqlsrv'), TRUE)) {
            $this->display_error('', 'PDO: Invalid or non-existent subdriver', false);
            $this->dsn = NULL;
        }

    }

    public function db_connect($persistent)
    {
        $this->options[PDO::ATTR_PERSISTENT] = $persistent;
        try {
            return new PDO($this->dsn, $this->username, $this->password, $this->options);
        } catch (PDOException $e) {
            $this->display_error('', $e->getMessage(), TRUE);
            return FALSE;
        }

    }

    protected function _execute($sql)
    {
        return $this->conn_id->query($sql);
    }

    /**
     * 事务开启
     * @return bool
     */
    public function trans_begin()
    {
        //当事务被嵌套，我们只 begin/commit/rollback的最外面的
        if (!$this->trans_enabled OR $this->_trans_depth > 0) {
            return TRUE;
        }

        //重置事务失败标志。
        //将$TEST_MODE标志被设置为TRUE，事务将被回滚
        //即使查询产生一个成功的结果。
        //   $this->_trans_failure = ($test_mode === TRUE);
        return $this->conn_id->beginTransaction();
    }

    /**
     * 事务提交
     * @return bool
     */
    public function trans_commit()
    {
        if (!$this->trans_enabled OR $this->_trans_depth > 0) {
            return TRUE;
        }

        return $this->conn_id->commit();
    }

    /**
     * 事务回滚
     * @return bool
     */
    public function trans_rollback()
    {
        if (!$this->trans_enabled OR $this->_trans_depth > 0) {
            return TRUE;
        }

        return $this->conn_id->rollBack();
    }

}


