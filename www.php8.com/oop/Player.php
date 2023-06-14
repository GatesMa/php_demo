<?php

/**
 * php 类与对象
 */
// NBA球员类
class Player
{
    // 成员属性前要有访问修饰符 public private protected
    // public 默认的, 关键词定义类内、类外、子类都可见
    // protected 关键词定义类内、子类可见，类外不可见
    // private 关键词定义类内可见, 子类、类外不可见
    public $height;
    public $name;
    // protected属性只能由本类或者子类访问
    protected $num;
    public $team;
    // private属性只能由本类访问
    private $weight;


    // 如果给private protected属性赋值
    // 魔术函数 : __set __get __call __callStatic
    // 构造函数 构造器 类每实例化一次 构造函数自动被调用
    public function __construct($name, $height, $team, $num, $weight)
    {
        // 类成员之间的互相访问  $this 本对象
        // 1.初始化类成员 让类/对象的状态稳定下来
        // 2.给对象的属性进行初始化赋值
        // 3.给私有或者受保护的成员属性赋值
        $this->name = $name;
        $this->height = $height;
        $this->team = $team;
        $this->num = $num;
        $this->weight = $weight;
    }

    // 成员方法
    public function jog()
    {
        //echo $str;
        return "$this->name is jogging, weight: {$this->weight}, num: {$this->num}";
    }

    public function shoot()
    {
        echo 'is shooting<br>';
    }

    // public function __get($name) {
    //     echo "Get field {$name}\n";
    //     return $this->$name;
    // }
}

// echo '<pre>';
//
// // new关键字完成类的实例化 得到对象
// // $pdo = new PDO; $pdo->prepare()
// $jordan = new Player('jordan', '195cm', 'Bull', 23, '80kg');
// // 给对象成员属性赋值
// $jordan->height = '198cm';
// // 访问对象成员属性
// var_dump($jordan->height);
// // $jordan->weight = '80kg'; //Cannot access private property Player::$weight
// // $jordan->num = 23; //annot access protected property Player::$num
// var_dump($jordan->jog());













// $james = new Player;
// $james->height = '205cm';
// var_dump($james->height);
// var_dump($jordan, $james);
