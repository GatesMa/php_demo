<?php

// 索引数组，下标为整型
$arr = [1, 2, 4, 5];
var_dump($arr);

var_dump(hash33('123'));

/**
 * 对字符串进行Time33算法计算出hash值
 *
 * @param string $str
 *
 * @return int
 */
function hash33($str)
{
    $hash = 5381;
    $len = strlen($str);
    if ($len === 0) {
        return $hash;
    }
    for ($i = 0; $i < $len; ++$i) {
        $hash = (int)(($hash << 5 & 0x7fffffff) + ord($str{$i}) + $hash);
    }
    return $hash & 0x7fffffff;
}

?>