# 拦截器

<!-- toc -->

在这一篇文章中，将对SPAPHP框架中拦截器的 **使用与实现** 进行介绍

## 1. 什么是拦截器

拦截器简单的说就是一种对象，它可以对正常路由方法的执行进行干预，在之前或之后加入其他处理逻辑

通常它可以有以下这些用途

- 日志记录，比如说在调用某个方法前记下日志，便于信息统计
- 权限检查，执行路由方法前检查用户是否登录，没有直接返回登录页面
- 性能监控，可以利用拦截器在路由方法前和后分别记录时间，从而得出每次路由方法执行耗时
- ...

## 2. SPAPHP中拦截器

这里我们首先介绍一下在SPAPHP框架当中如何定义与使用拦截器，再说明多个拦截器存在时，执行流程是怎样的

### 2.1 拦截器的定义

SPAPHP中一个拦截器类的定义如下

```php
class ErrorHandler
{
    public function handle($request, \Closure $next, $a, $b)
    {
        // 这部分在路由方法前执行
        echo 'before route action';
        // 路由方法
        $response = $next($request);
        // 这部分在路由方法后执行
        echo 'after route action';
        return $response;
    }
}
```

每个拦截器都要实现handle这个方法，方法至少接收$request和$next两个参数，也可以传递自己需要的额外参数，如示例中的$a和$b，handle方法中会有这样一句

```php
$response = $next($request);
```

在这句前的语句都会在路由方法调用前执行，这句后的语句则是在路由方法调用后执行

### 2.2 拦截器的使用

拦截器的通常是在应用的启动文件当中定义的(app.php文件或是bootstrap.php文件当中)

拦截器有两种，全局拦截器和路由拦截器

全局拦截器在所有路由上都会生效，而路由拦截器则是在所定义的特定路由上生效

#### 2.2.1 全局拦截器

```php
// interceptor
$app->interceptor(
    [
        \app\interceptor\ErrorHandler::class,
    ]
);
```

#### 2.2.2 路由拦截器

```php
// web routes
$app->router->group([
    'namespace' => 'app\\controller',
    'interceptor' => [
        \app\interceptor\ErrorHandler::class,
    ],
], function (\spaphp\routing\Router $router) {
    require __DIR__ . '/../routes/web.php';
});
```

**拦截器别名定义**

在SPAPHP中还会看到有下面这样的用法

```php
// route interceptor
$app->routeInterceptor([
    'error1' => \app\interceptor\ErrorHandler1::class,
    'error2' => \app\interceptor\ErrorHandler2::class,
    'error3' => \app\interceptor\ErrorHandler3::class,
]);
```

这里其实是为 **路由拦截器** 定义了一个别名，比如上面这样定义后，路由定义可以进行如下简化

```php
// web routes
$app->router->group([
    'namespace' => 'app\\controller',
    'interceptor' => [
        'error1:a,b',
        'error2:a,b',
        'error3:a,b',
    ],
    // 下面这样定义多个拦截器也可以
    // 'interceptor' => 'error1:a,b|error2:a,b|error3:a,b';
], function (\spaphp\routing\Router $router) {
    require __DIR__ . '/../routes/web.php';
});
```

在上面的示例中，利用别名来定义拦截器，同时还传递了a、b两个参数，注意一下定义路由拦截器定义多个时候可以使用数组或是字符串这两种方式

### 2.3 拦截器的执行流程

我们通过一个简单的示例来说明拦截器的执行流程

假设项目录当中对路由地址 /version 定义了拦截器如下

```php
$app->router->group([
    'interceptor' => [
        Example1::class,
        Example2::class,
        Example3::class,
    ], function ($router) {
        $router->get('/version', function () use ($router) {
            echo 'hello';
        });
    });
```

并且每个拦截器的实现是这个样子的

```php
class Example1
{
    public function handle($request, \Closure $next)
    {
        echo 'Example1';
        $response = $next($request);
    }
}
```

```php
class Example2
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        echo 'Example2';
    }
}
```

```php
class Example3
{
    public function handle($request, \Closure $next)
    {
        echo 'Example3 before';
        $response = $next($request);
        echo 'Example3 after';
    }
}
```

框架对执行流的组装过程分成如下两步

#### 2.3.1 拦截器数组进行逆序排列

```php
'interceptor' => [              'interceptor' => [
    Example1::class,                Example3::class,
    Example2::class,     =>         Example2::class,
    Example3::class,                Example1::class,
]                               ]
```

#### 2.3.2 组装执行流

<img src="img/interceptor/interceptor_1.png" height="400" width="900" />

从这里也看到，如果拦截器之间有关联的话，它们之间顺序安排也要注意


## 3. 拦截器是如何实现的

首先，我们仿照SPAPHP框架中拦截器执行流的实现，给出下面的示例

```php
<?php

class FirstInterceptor
{
    public static function handle(Closure $next)
    {
        echo 'first interceptor before'. PHP_EOL;
        $next();
        echo 'first interceptor after'. PHP_EOL;
    }
}

class SecondInterceptor
{
    public static function handle(Closure $next)
    {
        echo 'second interceptor before'. PHP_EOL;
        $next();
        echo 'second interceptor after'. PHP_EOL;
    }
}

class ThirdInterceptor
{
    public static function handle(Closure $next)
    {
        echo 'third interceptor before'. PHP_EOL;
        $next();
        echo 'third interceptor after'. PHP_EOL;
    }
}

function carry($stack, $step)
{
    return function () use ($step, $stack) {
        return $step::handle($stack);
    };
}

function then()
{
    $steps = ['FirstInterceptor', 'SecondInterceptor', 'ThirdInterceptor'];
    $prepare = function () {
        echo '路由方法'. PHP_EOL;
    };
    $go = array_reduce($steps, "carry", $prepare);
    $go();
}

then();
```

将该示例保存为php文件并运行，可以得到如下的输出示例

```shell
third interceptor before
second interceptor before
first interceptor before
路由方法
first interceptor after
second interceptor after
third interceptor after
```

理解示例实现的简单的拦截器的关键在于array_reduce这个函数，在该示例中，carry函数用$prepare参数作为初始项，不断从$steps数组中取出单个元素，组装成新的Closure对象

下面再看下SPAPHP框架中的实现

```php
$response = (new PipeLine($this))
                    ->send($request)
                    ->through($interceptor)
                    ->then(function ($request) use ($routeInfo) {
                        return $this->runRoute($routeInfo);
                    });

public function then(Closure $destination)
{
    $pipeline = array_reduce(
        array_reverse($this->pipes), // 这里先调用了array_reverse逆序了一下拦截器数组
        $this->carry(),
        $this->prepareDestination($destination)
    );

    return $pipeline($this->passable); // 这里的passable对象就是$request对象
}
```

对比这个实现与示例中给的，基本是一致的，只有以下两点不同

1. 样例中没有演示数据流的传递，都是直接输出，实际的闭包多了一个$request参数，同时会返回$response对象，如下所示，这也和前面介绍的拦截器的定义相符
```php
class FirstInterceptor
{
    public static function handle(Closure $next)
    {
        echo 'first interceptor before'. PHP_EOL;
        $response = $next($request); // <--- 实际用$request对象来传递数据
        echo 'first interceptor after'. PHP_EOL;
        return $response; // <--- 返回$response作为输出
    }
}
```

2. 执行array_reduce前先用array_reverse方法对拦截器数组进行了逆序，这样一来，达到了先定义的前置拦截器先执行、先定义的后置拦截器后执行的效果
