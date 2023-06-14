<?php

require 'autoload.php';


echo 'Client.php';
echo '<pre>';

// new关键字完成类的实例化 得到对象
// $pdo = new PDO; $pdo->prepare()
$jordan = new Player('jordan', '195cm', 'Bull', 23, '80kg');
// 给对象成员属性赋值
$jordan->height = '198cm';
// 访问对象成员属性
var_dump($jordan->height);
// $jordan->weight = '80kg'; //Cannot access private property Player::$weight
// $jordan->num = 23; //annot access protected property Player::$num
var_dump($jordan->jog());

