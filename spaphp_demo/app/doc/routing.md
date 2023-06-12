## Routing

### Router
路由器支持3种模式的路由
1. 基于配置的
2. 基于rest的
3. 基于规则的

#### 基于配置的
```
<?php
// 简写方式
$router->get('/', 'IndexController@index');
$router->post('/test', 'TestController@store');

// 完整方式，等同于第一条路由规则
$router->addRoute('GET', '/', 'IndexController@index');

// facade 模式
Router::get('/', 'IndexController@index');
Router::post('/test', 'TestController@store');
Router::addRoute('GET', '/', 'IndexController@index');

```

#### 基于rest的
```
<?php

// 实例模式
$router->rest('/photo', 'TestController');

// facade 模式
Router::rest('/member', 'MemberController');

```

> REST 对应关系

METHOD      |URI                     |响应
---|---|---
GET	        |/photo	                |index
GET	        |/photo/create	        |create
POST	    |/photo	                |store
GET	        |/photo/{photo}	        |show
GET	        |/photo/{photo}/edit	|edit
|PUT/PATCH	|/photo/{photo}	        |update
DELETE	    |/photo/{photo}	        |destroy

#### 基于规则的
```

<?php
// facade 模式
Router::regexp('/v1/{controller}/{action}', function($controller, $action){
    return $controller . 'Controller@'.$action;
});

// 实例模式
$router->regexp('/v1/{controller}/{action}', function($controller, $action){
    return $controller . 'Controller@'.$action;
});

```


