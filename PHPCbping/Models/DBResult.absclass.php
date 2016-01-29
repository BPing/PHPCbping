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
     * Custom Result Object
     *
     * @var    object[]
     */
    public $custom_result_object = array();

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




//int mysqli_field_tell ( mysqli_result $result )
//bool data_seek ( int $offset )
//mixed fetch_all ([ int $resulttype = MYSQLI_NUM ] )
//mixed fetch_array ([ int $resulttype = MYSQLI_BOTH ] )
//array fetch_assoc ( void )
//object fetch_field_direct ( int $fieldnr )
//object fetch_field ( void )
//array fetch_fields ( void )
//object fetch_object ([ string $class_name = "stdClass" [, array $params ]] )
//mixed fetch_row ( void )
//int mysqli_num_fields ( mysqli_result $result )
//bool field_seek ( int $fieldnr )
//void free ( void )
//array mysqli_fetch_lengths ( mysqli_result $result )
//int mysqli_num_rows ( mysqli_result $result )

}