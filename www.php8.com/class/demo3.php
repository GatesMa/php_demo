<?php

/**
 * 方法拦截器 当访问当前类中不存在的方法时或者不可见的方法时 会调用魔术方法 __call   __callStatic
 */


/**
 * 事件委托 查询构造器
 */



class User
{
    public function normal()
    {
        return __METHOD__;
    }


    // 当访问当前类中不存在的普通方法时或者不可见的普通方法时 会自动调用__call
    public function __Call(string $method, array $args)
    {
        // var_dump($args);
        // echo 'The method you are calling ' . __CLASS__ . '::' . $method . ' does not exsit or it can not be accessed';
        printf('调用当前类中不存在的普通方法%s(),参数列表为[%s]<br>', $method, implode(',', $args));
    }


    // 当访问当前类中不存在的静态方法时或者不可见的静态方法时 会自动调用__callStatic
    public static function __CallStatic(string $method, array $args)
    {
        // var_dump($args);
        echo 'The static method you are calling ' . __CLASS__ . '::' . $method . ' does not exsit or it can not be accessed' . "\n";
        printf('调用当前类中不存在的静态方法%s(),参数列表为[%s]<br>', $method, implode(',', $args));
    }
}

echo '<pre>';

$u = new User;
echo $u->normal();


// echo "\n" . __CLASS__ . "\n";

$u->login('张三', 'qweere');
User::index('古力娜扎', '172cm', '48kg');
