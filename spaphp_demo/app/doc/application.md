# 框架概述

<!-- toc -->

## 1. 框架使用原则

SPAPHP框架以容器为核心，推荐在项目中所使用的类都注册到容器当中，通过容器进行创建

## 2. 执行流程

框架设定好的执行流程分为以下几步，有大体了解即可

1. 执行初始化组件
2. 获取路由结果
3. 根据路由结果，获取拦截器和Controller业务流
4. 执行拦截器和Controller业务流程调用栈
5. 输出或返回

## 3. 如何引入框架

> 在[安装配置](quickStart.md)章节中已经基于SPAPHP框架创建了自己的项目，这里只是介绍一下该项目是如何引入框架的

项目核心启动流程文件app.php位于src/bootstrap目录下，这个文件是项目与框架结合的关键，大体内容如下

```php
<?php
// 获取Application对象
$app = new \spaphp\Application(__DIR__);

// 设置初始化组件(当前没有用到这个功能)
$app->bootstrappers([]);

// 设置拦截器
$app->interceptor([
    AuthInterceptor::class,
    CSRFInterceptor::class,
]);

// 设置路由拦截器
$app->routeInterceptor([
    'auth' => AuthInterceptor::class,
    'csrf' => CSRFInterceptor::class,
]);

// 注册服务提供者
$app->register(AppServiceProvider::class);

// 配置路由规则
$app->router->group([
    'namespace' => 'app\\controller',
    'prefix' => '/v1',
], function ($router) {
    require __DIR__ . '/routes/web.php';
});

// 执行
$app->run();

```

可以看到，项目引用框架分为两步

1. 获取Application对象

    框架对外提供Application对象，该对象继承自容器对象，同时注册了路由、日志等框架内置组件，**获取Application对象** 就可以得到框架提供的能力

2. 针对项目定制

    一般来说，项目都需要设置以下内容

    * 配置初始化组件
    * 配置拦截器
    * 注册服务提供者
    * 配置路由规则
