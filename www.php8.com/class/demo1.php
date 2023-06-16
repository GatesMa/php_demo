<?php
echo '<pre>';
/**
 * 后期（延迟）静态绑定   static
 */


/**
 * 在创建类层次结构时，self关键字在编译时就已经确定了它的作用范围，而不是在运行时（后期），self不能动态地与调用类进行绑定。解决办法：用static替换self进行后期（延迟）静态绑定。
 *
 *
 * $this能动态地与调用类进行绑定。
 */

class Employee
{
    public static $favSport = 'football';
    public static function watchTv()
    {
        // echo 'watching ' . self::$favSport . '<br>';
        echo 'watching ' . static::$favSport . '<br>';
    }
}

class Execute extends Employee
{
    public static $favSport = 'Polo';
}


// echo Execute::watchTv(); //watching football
//ob_clean();


// 单例模式 只允许类被实例化一次
class Father
{
    private function __construct()
    {
    }

    private function __clone()
    {
    }

    // 存储father类的实例
    protected static $_instance;

    // 获取father类的实例 唯一实例
    static function getInstance()
    {
        if (static::$_instance === null) {

            //获取类的实例
            //1. self 始终永远和当前类进行绑定
            //2. self 在静态继承上下文中, 不能动态的识别或者设置静态成员的调用者
            static::$_instance = new static;
        }
        // 返回对象
        return static::$_instance;
    }
}



class Son extends Father
{
    protected static $_instance;
}


class Son1 extends Father
{
    protected static $_instance;
}
var_dump(Father::getInstance());




var_dump(Son::getInstance());
var_dump(Son1::getInstance());
