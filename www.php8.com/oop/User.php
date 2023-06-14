<?php

/**
 * php 类与对象
 */
// NBA球员类
class User
{
    // 成员属性前要有访问修饰符 public private protected
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

        echo "$this->name is jogging,weighing {$this->weight}{$this->num}<br>";
    }

    public function shoot()
    {
        echo 'is shooting<br>';
    }
}