# 控制器篇

<!-- toc -->

## 1. 对控制器的理解

控制器是处理用户请求时的一个入口，通常将项目划分为 控制器、业务逻辑、数据访问等不同层次，便于组织代码

从根本上来说，控制器只是一个逻辑上的概念，任何类都可以成为控制器，真正起决定作用的是 **路由组件，路由决定了每次请求的入口程序在哪**

## 2. SPAPHP中控制器的使用

SPAPHP框架中控制器的实现十分简单，代码如下

```php
abstract class Controller
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->init();
    }

    protected function init()
    {
    }
}
```

这里就做了一件事，那就是自动注入Application类对象

在自己项目当中的控制器，可以继承自SPAPHP提供的控制器，这样就可以直接用$this->app的方式得到容器，从而得到一切。。。

控制器的主要任务是接收输入、调用Service层逻辑、返回输出，SPAPHP中通过Request类与Response类来分别封装了输入与输出，在项目当中可以用如下方法得到这两个类对象

```php
1. 继承自Controller类（或者自行注入Application对象）

$this->app['request'];
$this->app['response'];

这种方式使用当前类的属性$app来进行操作

2. Facade模式调用

App::make('request');
App::make('response');

相比来说，Facade方式调用比较简单，并且可以在任何地方使用

```

这里的Facade调用模式会在 Facade篇 中再另行介绍，先这样使用即可

你可能会好奇当前请求的Request对象与Response对象是在什么时候进行绑定的呢

其实是在框架当中的 RouteRequest 这个Trait中实现的

```php
protected function dispatch(Request $request = null, Response $response = null)
{
    try {
        $this->bootstrap();
        if ($request === null) {
            $request = Request::getInstance();
        }
        $this->instance('request', $request); // <---- 实例绑定Request对象
        if ($response === null) {
            $response = Response::create();
        }
        $this->instance('response', $response); // <---- 实例绑定Response对象
        ...
        // 以下代码省略
    } catch (Exception $e) {
        return $this->handleUncaughtException($e);
    } catch (Throwable $e) {
        return $this->handleUncaughtException($e);
    }
}
```

