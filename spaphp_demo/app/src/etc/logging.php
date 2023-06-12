<?php

use spaphp\facade\App;

return [
    'name' => 'logTest',

    'rootLogger' => [
        'single',
//        'daily',
    ],

    'single' => [
        'handler' => 'single',
        'path' => App::varPath('log/spaphp.log'),
        'level' => 'debug',
    ],

    'daily' => [
        'handler' => 'daily',
        'path' => App::varPath('log/spaphp.log'),
        'level' => 'debug',
        'days' => 7,
    ],

];
