<?php

/**
 * php重载  overload
 * php重载 属性，  是指动态地创建类属性和方法。我们是通过魔术方法（magic methods）来实现的。 __get __set __callStatic __call
 */
// 当访问类中不存在或者不可见的类成员时，会自动调用魔术方法__set __get
// 因为魔术方法都是公开的，所以一些私有成员的不可见性就不会生效

class Credit
{
    public $name;
    private $idNum;
    public function __construct($name, $idNum)
    {
        $this->name = $name;
        $this->idNum = $idNum;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
        echo "\n[set] $name : $value\n";
        return $this->$name;
    }


    public function __get($name)
    {
        echo "\n[get] $name\n";
        return $this->$name;
    }
}

echo '<pre >';

$c = new Credit('胡歌', '341621198501215484');
$c->age = 20; //__set
echo $c->age; //__get

$c->id = 'idddddd'; //__set
echo $c->id;

$c->idNum = "121313141";
echo $c->idNum;