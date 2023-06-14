<?php
// php变量类型            php弱类型脚本语言

/**
 * 4种标量类型  整型 字符串 布尔型 浮点型
 * 2复合类型 数组 对象
 * 2特殊类型 resource null
 */
// =赋值符号
$username = 21;
$password = 'dhskdhskgdusgdsjgdkhdkshd';
$boolean = true; //布尔类型用于条件判断
$float = 25.25;
print_r("<pre>");



$foo = 21;
$foo = '你好';
echo $foo;
var_dump($foo);

// 数组 按照维度划分 一维数组 多维数组
// 索引数组 下标为整型
$arr = [25, 23, 58, 56];
var_dump($arr);
// 关联数组 下标为字符串
$user = ['id' => 1, 'name' => '张三', 'email' => '95564545@qq.com', '21412541'];
var_dump($user);
var_dump($user[0]);

// ob_clean(); //清空缓冲区的内容



// 多维数组
$users = [
    ['id' => 1, 'name' => '张三', 'email' => '95564545@qq.com'],
    ['id' => 2, 'name' => 'Peter', 'email' => '1245545@qq.com'],
    ['id' => 3, 'name' => 'Chloe', 'email' => '9674545@qq.com']
];

var_dump($users);


// 对象 OOP 类实例化的结果
$obj = new stdClass;
var_dump($obj);//object(stdClass)[1]




$test_null = [];
var_dump(isset($test_null[0]));







echo '<hr>';





$closure = function($name) {
    return 'name:' . $name;
};
echo $closure('gatesma');













print_r("</pre>");






