<?php

$foo = "0123456789a123456789b123456789c";

var_dump(strrpos($foo, '7', -5));  // 从尾部第 5 个位置开始查找
var_dump(strrpos($foo, '7'));  // 从尾部第 5 个位置开始查找
// 结果: int(17)

var_dump(strrpos($foo, '7', 20));  // 从第 20 个位置开始查找
// 结果: int(27)

var_dump(strrpos($foo, '7', 28));  // 结果: bool(false)