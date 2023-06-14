<?php

/**
 * 回调函数：php回调是指在主线程函数执行的过程中,突然跳去执行设置的回调函数,回调函数执行结束后, 再回到主线程处理接下来的流程
 *
 *
 * 匿名函数最通常作为回调函数的参数使用
 *
 *
 * 你告诉妹妹回到家（不确定）//回调    给我发短信（一定）
 * 你//主线程函数  你该干嘛干嘛
 */


// 给到一个任意数组，把数组中的偶数筛选出来，组成一个新数组 返回 然后计算新数组所有偶数的和
require_once 'demo2.php';
$odd = function (array $arr) {
    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i] % 2 == 0) {
            $newArr[] = $arr[$i];
        }
    }

    return $newArr;
};

$arr = [34, 89, 68, 23, 443, 234, 2, 25, 45, 89, 78, 452, 3434];

var_dump($odd($arr));

/**
 * params 第一个参数是一个匿名函数，第二个参数是一个任意数组
 */
function sum(Closure $func, $arr)
{
    $oddArr = $func($arr);
    return array_sum($oddArr);
}
echo '<hr>';
echo sum($odd, $arr);

//php脚本是单线程，脚本是同步执行的，如果遇到耗时函数将会发生线程阻塞的问题，应该将它改为异步回调的方式执行

//  call_user_func()把第一个参数作为回调函数调用
// call_user_func_array()
// 回调的是全局函数
echo '<hr>';


var_dump(sayhello('爆龙战士'));

var_dump(call_user_func_array('sayhello', ['爆龙战士']));

var_dump(call_user_func('sayhello', '爆龙战士'));

$hello = "sayhello";
var_dump($hello('爆龙战士'));

// 回调匿名函数
// var_dump(call_user_func($odd, $arr));




// <!--<script>-->
// <!--    $('.btn').click(function() {-->
// <!---->
// <!--    })-->
// <!--</script>-->