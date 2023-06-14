<?php
print_r("<pre>");
/**
 * 函数属于全局成员
 * 特殊形式的函数
 * 1.匿名函数（闭包函数）
 * 2.回调函数
 * 3.递归函数
 */

echo sayhello('张学友');
function sayhello(string $name): string //返回string类型
{
    // var_dump($name);
    return "{$name},欢迎您下榻喜来登酒店。";
}

echo sayhello('张学友');





// echo $closure('张老师');
echo '<hr>';

// 匿名函数 :通常会被当做回调函数的参数来使用。
$closure = function ($name) {
    return "{$name},欢迎您下榻喜来登酒店。";
};
echo $closure('朱老师');
// ob_clean();
// 闭包引来（变量）作用域的问题
echo '<hr>';

// 全局变量是指声明在函数外部的变量，在函数内部访问不到。
// 局部变量是指声明在函数内部的变量，只能在函数内部被访问到。
$name = '灭绝老师';
$email = 'imzchloe.@gmail.com';

function greeting()
{
    $modify = '美丽的';
    // 1. global
    // 2. 超全局数组$GLOBALS   $_GET $_POST
    // global $name, $email;
    return "$modify{$GLOBALS['name']}您好!，恭喜您邮箱注册成功，账号为{$GLOBALS['email']}";

    // return "{$name}您好，恭喜您邮箱注册成功，账号为{$email}";
}
// echo $modify;
echo greeting();

echo '<hr>';

// 3. 闭包函数借助关键字use

$c = function ($modify) use ($email) {
    global $name;
    return "{$modify}{$name}您好，恭喜您邮箱注册成功，账号为{$email}";
};

echo $c('美丽的');

// 闭包改变变量上下文的值 需要引用传递
$c1 = function ($newName) use (&$name) {
    $name = $newName;
};
$c1('西门老师');
echo $name; //全局变量的值被改变~


