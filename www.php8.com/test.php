<?php

echo '<pre>';

$json = '[   {     "product_type_array": [30],     "product_type_name": "商品推广"   },   {     "product_type_array": [19, 12, 38, 35, 20, 4, 50],     "product_type_name": "应用推广"   },   {     "product_type_array": [1000],     "product_type_name": "网页推广"   },   {     "product_type_array": [43],     "product_type_name": "销售线索收集"   },   {     "product_type_array": [31, 29],     "product_type_name": "品牌活动推广"   },   {     "product_type_array": [46, 49],     "product_type_name": "小游戏推广"   },   {     "product_type_array": [39, 41],     "product_type_name": "门店推广"   },   {     "product_type_array": [36],     "product_type_name": "派发优惠券"   },   {     "product_type_array": [23],     "product_type_name": "推广我的公众号"   },   {     "product_type_array": [0, 1, 2, 3, 5, 6, 7, 8, 9, 10, 11, 13, 14, 15, 16, 17, 21, 22, 24, 25, 26, 27, 28, 32, 33, 34, 37, 40, 42, 44, 45, 47, 48, 51, 52, 53, 1001],     "product_type_name": "其他"   } ]';


var_dump($json);

$arr = json_decode($json, true);
var_dump($arr[1]['product_type_array']);


$obj = json_decode($json, false);
var_dump($obj[0]->product_type_array);

const RPT_SUMMARY_FIELDS = [
    'cost',
    'cash_cost',
    'credit_cost',
    'virtual_cost',
    'gift_cost',
    'redcover_cost',
    'insourced_give_cost',
    'insourced_turnover_cost',
    'return_goods_cost',
    'pay_virtual_cost',
    'gift_android_cost',
    'wechat_tcc_cost',
    'gift_restrict_cost',
    'mutual_select_cost',
    'sub_publisher_gift',
];
$returnSummary = array_flip(RPT_SUMMARY_FIELDS);
print_r($returnSummary);

