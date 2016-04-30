<?php

/**
 * 数据结果集处理类
 * @author cbping
 * Class DBResult
 */
abstract class DBResult
{
    /**
     * Connection ID
     *
     * @var    resource|object
     */
    public $conn_id;

    /**
     * Result ID
     *
     * @var    resource|object
     */
    public $result_id;

    /**
     * Result Array
     *
     * @var    array[]
     */
    public $result_array = array();

    /**
     * Result Object
     *
     * @var    object[]
     */
    public $result_object = array();

    /**
     * Current Row index
     *
     * @var    int
     */
    public $current_row = 0;

    /**
     * Number of rows
     *
     * @var    int
     */
    public $num_rows;

    /**
     * Row data
     *
     * @var    array
     */
    public $row_data;

    /**
     * @param object $driver_object
     */
    public function __construct(& $driver_object)
    {
        $this->conn_id = $driver_object->conn_id;
        $this->result_id = $driver_object->result_id;
    }

    public function num_rows()
    {
        if (is_int($this->num_rows)) {
            return $this->num_rows;
        } elseif (count($this->result_array) > 0) {
            return $this->num_rows = count($this->result_array);
        } elseif (count($this->result_object) > 0) {
            return $this->num_rows = count($this->result_object);
        }

        return $this->num_rows = count($this->result_array());
    }

    /**
     * 获取所有结果集
     *
     * @param string $arg_type 'object' or 'array'
     * @return mixed false|array
     */
    public function result($arg_type = 'array')
    {
        if ($arg_type === 'array') {
            return $this->result_array();
        } elseif ($arg_type === 'object') {
            return $this->result_object();
        }
        return false;
    }

    /**
     *获取所有结果集 object
     */
    public function result_object()
    {
        if (count($this->result_object) > 0) {
            return $this->result_object;
        }

        //result_id变量不是一个有效的资源，所以我们将简单地返回一个空数组。
        if (!$this->result_id)// OR $this->num_rows === 0
        {
            return array();
        }

        if (($c = count($this->result_array)) > 0) {
            for ($i = 0; $i < $c; $i++) {
                $this->result_object[$i] = (object)$this->result_array[$i];
            }

            return $this->result_object;
        }
        //位移归零
        is_null($this->row_data) OR $this->data_seek(0);

        if ($all = $this->_fetch_all('object')) {
            return $this->result_object = $all;
        }

        while ($row = $this->_fetch_object()) {
            $this->result_object[] = $row;
        }

        return $this->result_object;
    }

    /**
     *获取所有结果集 array
     */
    public function result_array()
    {

        if (count($this->result_array) > 0) {
            return $this->result_array;
        }

        //result_id变量不是一个有效的资源，所以我们将简单地返回一个空数组。
        if (!$this->result_id) { //OR $this->num_rows === 0
            return array();
        }

        if (($c = count($this->result_object)) > 0) {
            for ($i = 0; $i < $c; $i++) {
                $this->result_array[$i] = (array)$this->result_object[$i];
            }

            return $this->result_array;
        }
        //位移归零
        is_null($this->row_data) OR $this->data_seek(0);

        if ($all = $this->_fetch_all('array')) {
            return $this->result_array = $all;
        }

        while ($row = $this->_fetch_assoc()) {
            $this->result_array[] = $row;
        }

        return $this->result_array;
    }
    // --------------------------------------------------------------------
    /**
     * 获取某一行数据
     *
     * @param int $n
     * @param string $arg_type 'object' or 'array'
     * @return false|array
     */
    public function row($n = 0, $arg_type = 'array')
    {
        if (!is_numeric($n)) $n = 0;
        if ($arg_type === 'object') return $this->row_object($n);
        elseif ($arg_type === 'array') return $this->row_array($n);
        else return false;
    }

    // --------------------------------------------------------------------
    /**
     * 获取下一行数据
     *
     * @param string $arg_type 'object' or 'array'
     * @return mixed
     */
    public function fetch($arg_type = 'array')
    {
        $result = $this->result($arg_type);
        if (count($result) === 0) {
            return NULL;
        }

        return isset($result[$this->current_row + 1])
            ? $result[++$this->current_row]
            : NULL;
    }

    // --------------------------------------------------------------------

    /**
     * 获取某一行数据 - object
     *
     * @param    int $n
     * @return    object
     */
    public function row_object($n = 0)
    {
        $result = $this->result_object();
        if (count($result) === 0) {
            return NULL;
        }

        if ($n !== $this->current_row && isset($result[$n])) {
            $this->current_row = $n;
        }

        return $result[$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * 获取某一行数据 - array
     *
     * @param    int $n
     * @return    array
     */
    public function row_array($n = 0)
    {
        $result = $this->result_array();
        if (count($result) === 0) {
            return NULL;
        }

        if ($n !== $this->current_row && isset($result[$n])) {
            $this->current_row = $n;
        }

        return $result[$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * 结果集中的字段数目
     *
     * @return    int
     */
    public function num_fields()
    {
        return 0;
    }

    // --------------------------------------------------------------------

    /**
     * 取字段名
     *
     * 生成的列名称的数组。
     *
     * @return    array
     */
    public function list_fields()
    {
        return array();
    }

    // --------------------------------------------------------------------

//    /**
//     *字段数据
//     *
//     * 生成包含字段的元数据对象数组。
//     *
//     * @return    array
//     */
//    public function field_data()
//    {
//        return array();
//    }
    // --------------------------------------------------------------------

    /**
     * 调整结果指针的任意行中的结果
     * @param int $n
     * @return bool
     */
    public function data_seek($n = 0)
    {
        return FALSE;
    }

    /**
     * 释放结果集资源
     * 相当于 close()或者free()
     */
    public function free_result()
    {
        $this->result_id = FALSE;
    }

    /**
     * @param $arg_type 'object' or 'array'
     * @return mixed
     */
    protected function _fetch_all($arg_type = 'array')
    {
        return false;
    }

    protected function _fetch_assoc()
    {
        return array();
    }

    protected function _fetch_object($arg_class_name = 'stdClass')
    {
        return array();
    }
}