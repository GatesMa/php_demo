<?php

$students = [
    ['name' => 'John', 'age' => 20, 'grade' => 'A'],
    ['name' => 'Jane', 'age' => 22, 'grade' => 'B'],
    ['name' => 'Mike', 'age' => 21, 'grade' => 'A'],
];

// 提取所有学生的姓名
$names = array_column($students, 'name');
print_r($names);
// 输出：Array ( [0] => John [1] => Jane [2] => Mike )

// 提取所有学生的年龄和成绩
$details = array_column($students, 'grade');
print_r($details);


$accountList = array_column($students, null, 'age');
print_r($accountList);



$jsonData = '{
    "conf": {
      "page": 0,
      "page_size": 0,
      "total_num": 0,
      "total_page": 0
    },
    "list": [
      {
        "adgroup_diagnose_res": [
          {
            "adgroup_id": 0,
            "diagnose_type": 0,
            "partition_time": "string"
          }
        ],
        "cs_advertiser_id": 12345
      },
      {
        "adgroup_diagnose_res": [
          {
            "adgroup_id": 0,
            "diagnose_type": 0,
            "partition_time": "string"
          }
        ],
        "cs_advertiser_id": 67890
      }
    ]
  }';

// 解码JSON数据为PHP数组
$data = json_decode($jsonData, true);


print_r($data);
print_r(array_column($data['list'], 'cs_advertiser_id'));