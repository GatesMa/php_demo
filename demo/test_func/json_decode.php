<?php

$jsonString = '{"name": "John", "age": 30, "city": "New York"}';

// 解码为对象
$obj = json_decode($jsonString);
echo $obj->name; // 输出: John
var_dump($obj);

// 解码为关联数组
$arr = json_decode($jsonString, true);
echo $arr['age']; // 输出: 30
var_dump($arr);

$jsonString = '["agency_dashboard/get_agent_cost_rpt"]';
$path = json_decode($jsonString, true);
var_dump(in_array("agency_dashboard/get_agent_cost_rpt", $path));


$config = null;

var_dump(is_null($config));
var_dump(is_null(json_decode($config, true)));

(new Test())->printInfo();

class Test {

    public function printInfo () {
        var_dump(__CLASS__);
    }

}