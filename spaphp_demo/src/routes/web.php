<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/3/7
 * Time: 下午4:22
 */

/**
 * @var \spaphp\routing\Router $router
 */

$router->get('/version', function () use ($router) {
    return $router->app->version();
});


// for ping
$router->addRoute(['GET', 'HEAD'], '/ping', function () use ($router) {
    return 'pong!';
});
