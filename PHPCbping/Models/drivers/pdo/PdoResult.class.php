<?php

/**
 * Class PdoResult
 */
class PdoResult extends DBResult
{
    // --------------------------------------------------------------------
    /**
     * 结果集的行数目
     *
     * @return int
     */
    public function num_rows()
    {
        if (is_int($this->num_rows)) {
            return $this->num_rows;
        } elseif (count($this->result_array) > 0) {
            return $this->num_rows = count($this->result_array);
        } elseif (count($this->result_object) > 0) {
            return $this->num_rows = count($this->result_object);
        } elseif (($num_rows = $this->result_id->rowCount()) > 0) {
            return $this->num_rows = $num_rows;
        }

        return $this->num_rows = count($this->result_array());
    }

    // --------------------------------------------------------------------

    /**
     * 结果集中的字段数目
     *
     * @return    int
     */
    public function num_fields()
    {
        return $this->result_id->columnCount();
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
        $field_names = array();
        for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
            //Warning：并非所有 PDO 驱动都支持 PDOStatement::getColumnMeta()。
            $field_names[$i] = @$this->result_id->getColumnMeta($i);
            $field_names[$i] = $field_names[$i]['name'];
        }

        return $field_names;
    }
//
//    // --------------------------------------------------------------------
//
//    /**
//     *字段数据
//     *
//     * 生成包含字段的元数据对象数组。
//     *
//     * @return    array
//     */
//    public function field_data()
//    {
//        try {
//            $retval = array();
//
//            for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
//                //Warning：并非所有 PDO 驱动都支持 PDOStatement::getColumnMeta()。
//                $field = $this->result_id->getColumnMeta($i);
//
//                $retval[$i] = new stdClass();
//                $retval[$i]->name = $field['name'];
//                $retval[$i]->type = $field['native_type'];
//                $retval[$i]->max_length = ($field['len'] > 0) ? $field['len'] : NULL;
//                $retval[$i]->primary_key = (int)(!empty($field['flags']) && in_array('primary_key', $field['flags'], TRUE));
//            }
//
//            return $retval;
//        } catch (Exception $e) {
//            $this->conn_id->display_error('', 'db_unsupported_feature');
//        }
//    }

    // --------------------------------------------------------------------

    /**
     * @return    void
     */
    public function free_result()
    {
        if (is_object($this->result_id)) {
            $this->result_id = FALSE;
        }
    }

    // --------------------------------------------------------------------
    /**
     * 获取结果集
     *
     * @param string $arg_type
     * @return mixed
     */
    protected function _fetch_all($arg_type = 'array')
    {
        if ($arg_type === 'object') {
            $arg_type = PDO::FETCH_ASSOC;
        } else {
            $arg_type = PDO::FETCH_CLASS;
        }
        return $this->result_id->fetchAll($arg_type);
    }

    // --------------------------------------------------------------------

    /**
     * 获取下一行
     *  关联数组
     * @return    array
     */
    protected function _fetch_assoc()
    {
        return $this->result_id->fetch(PDO::FETCH_ASSOC);
    }

    // --------------------------------------------------------------------

    /**
     * 获取下一行并作为一个对象返回。
     *
     * @param    string $class_name
     * @return    object
     */
    protected function _fetch_object($class_name = 'stdClass')
    {
        return $this->result_id->fetchObject($class_name);
    }
}
