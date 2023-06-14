<?php

/**
 * 类的自动加载器 前提 类名称跟类文件名称保持一致 psr-4规范
 */


spl_autoload_register(function ($className) {
    //检查要加载的类
    printf('类名：%s<br>', $className);

    // 要加载的类文件所在的绝对路径

    $file = __DIR__ . DIRECTORY_SEPARATOR . $className . '.php';

    // printf("\n" . __DIR__ . "\n");

    echo $file;

    if (!(is_file($file) && file_exists($file))) {
        throw new \Exception('文件名不合法或者文件不存在');
    }

    require $file;
});

// function __autoload($class) {
//     include  __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';// 'classes/' . $class . '.class.php';
// }


// spl_autoload_register(function ($classname) {
//     $file = __dir__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $classname . '.php';
//     if (!(is_file($file) && file_exists($file))) {
//         throw new \Exception('文件名不合法或者文件不存在');
//     }
//
//     require $file;
// });
