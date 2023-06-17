<?php

### WARNING: DON'T EDIT THIS FILE ###
### Created at 2018-11-20 08:16:42 ###

/**
 * @var \spaphp\routing\Router $router
 */

$router->addRoute("GET", "/photo", "app\\controller\\PhotoController@index");
$router->addRoute("GET", "/photo/create", "app\\controller\\PhotoController@create");
$router->addRoute("POST", "/photo", "app\\controller\\PhotoController@store");
$router->addRoute("GET", "/photo/{id}", "app\\controller\\PhotoController@show");
$router->addRoute("GET", "/photo/{id}/edit", "app\\controller\\PhotoController@edit");
$router->addRoute("PUT", "/photo/{id}", "app\\controller\\PhotoController@update");
$router->addRoute("DELETE", "/photo/{id}", "app\\controller\\PhotoController@destroy");
$router->addRoute("GET", "/", "app\\controller\\IndexController@index");
