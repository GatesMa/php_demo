# 执行流程

<!-- toc -->

在 **服务容器篇** 末尾提到，框架对外提供了Application类，这个类继承自Container类，应用可能通过Application类来使用框架提供的各种能力

在这一篇中，我们通过实际的例子分析一次完整的请求所经历的流程

**注意：下文的代码部分都经过简化，只保留了大体的流程**

## 1. 获取框架版本号

先根据 **安装配置** 篇当中的介绍，开启SPAPHP的Web服务

```shell
php -S localhost:8090 server.php
```

接下来在浏览器地址栏中输入 

```shell
http://localhost:8090/version
```

就可以看到如下输出

```shell
spaphp framework v2.0
```

接下来，就来看下是怎样得到这个输出结果的吧

## 2. “正式启动” 前的准备工作

请求首先到达的是server.php文件，该文件内容如下

```php
<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists($file = __DIR__ . '/src/web' . $uri)) {
    readfile($file);
    return;
}

require_once __DIR__ . '/src/web/index.php';
```

这里程序会解析出uri路径，并判断该路径是不是在访问文件

1. 如果是文件路径，读取该文件并返回
2. 如果不是，则转向index.php文件执行

显然 /version 这样的uri路径不是对应文件，接下来执行到index.php当中

```php
<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$app->run();
```

index.php内容同样比较简单

首先将autoload.php文件require进来，这是用于自动加载的文件，现在在程序中就可以访问vendor目录下(组件)当中提供的各种类了

然后，将app.php文件引入，得到$app这个变量，这里的$app其实就是Application类的实例

最后，执行$app->run()方法

在这里，已经看到了整个流程中最关键的app.php文件，在这个文件中定义了 **应用的核心启动流**

## 3. 应用核心启动流程

应用核心启动流程定义在app.php文件当中，该文件内容大体如下

```php
<?php
/**
 * 框架启动
 */
$app = new \spaphp\Application(realpath(__DIR__ . '/../'));

/**
 * 应用定制
 */

// Provider机制
$app->register(\app\provider\AppServiceProvider::class);
$app->register(\spaphp\metadata\MetadataServiceProvider::class);

// 全局拦截器
$app->interceptor(
    [
        \app\interceptor\ErrorHandler::class,
    ]
);

// 路由定义
$app->router->group([
    'namespace' => 'app\\controller',
    'interceptor' => [
        '\app\interceptor\Validate::class',  // <--- 路由拦截器
    ],
], function (\spaphp\routing\Router $router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
```

在app.php文件中的注释部分，将 **应用的核心启动流程** 又分为 **框架启动** 与 **应用定制** 两个部分，下面分别介绍

### 3.1 框架Application类启动流程

Application类的启动由构造器开始，内容如下

```php
public function __construct($path = null)
{
    $this->path = rtrim($path, '\/');
    $this->bootstrapContainer();
    $this->registerErrorHandler();
}
```

可以看到构造器中包含了设置项目根路径、启动容器、错误处理这三个部分，registerErrorHandler方法中将错误转换为异常，可以自行阅读

下面更进一步的看下bootstrapContainer方法

```php
protected function bootstrapContainer()
{
    static::setInstance($this);  
    $this->instance('app', $this);
    $this->enableFacade();
    $this->registerAliases();
}
```

1. static::setInstance($this) 将创建出来的Application实例赋值到$instance属性当中
2. $this->instance('app', $this) 绑定 'app' 到当前创建的实例上
3. $this->enableFacade()开启Facede功能，其实enableFacade所做的就是 **清空内部实例缓存** 和 **设置容器类的引用** 这两件事，后续介绍Facade机制的文章中再详细说明
4. $this->registerAliases()注册了一些别名

这里再补充一点，平常会见到$app['router']这样的取路由对象的用法，但是你知道'router'这个字符串是什么时候绑定到了路由对象上的吗

其实是Application类重写了Container类的make方法

```php
public $availableBindings = [
    ...
    'router' => 'routerBind',
];

public function make(string $abstract, array $params = [])
{
    ...
    if (array_key_exists($abstract, $this->availableBindings) &&
        !array_key_exists($this->availableBindings[$abstract], $this->bound)) {
        $method = $this->availableBindings[$abstract];
        $this->{$method}();
        $this->bound[$method] = true;
    }
    ...
}

protected function routerBind()
{
    $this->singleton('router', function () {
        return new Router($this);
    });
}
```

上面这三段代码逻辑说的是，make方法参数(比如说'router'), 能够在$availableBindings数组中找到配置的话，就先执行配置好的方法进行绑定，所以在routeBind方法中绑定'route'字符串到Router对象上了

### 3.2 应用对Application类的定制

介绍完框架Application类的启动部分，再来看下应用对Application类的定制，回顾一下app.php文件当中的内容，应用对Application类的设置主要有以下几个方面

#### 3.2.1 Provider机制

Provider机制是框架实现的一种 **约定**

```shell
如果一个组件想注册到框架当中，可以实现一个Provider类，这个Provider类提供register方法，在register方法当中，利用容器来注册这个组件的所有类，实际Application类的register方法也是调用的Provider类的register方法
```

如果这里还不是很清楚的话，看下AppServiceProvider类的实现明白了

#### 3.2.2 拦截器的设置

应用通常会设置自己的拦截器，在处理用户请求之前或之后进行一些特殊的处理

拦截器有两种，一种是路由拦截器，一种是全局拦截器

```php
// interceptor
$app->interceptor(
    [
        \app\interceptor\ErrorHandler::class,    // 全局拦截器
    ]
);
// app routes
$app->router->group([
    'interceptor' => [
        '\app\interceptor\ErrorHandler::class',  // 路由拦截器
    ],
], function (\spaphp\routing\Router $router) {
    require __DIR__ . '/../routes/cached_routes.php';
});
```

更详细的内容请见 **拦截器篇**

#### 3.2.3 路由设置

路由是每个应用都会定义的规则，它的作用就是把uri路径映射到处理函数上

```php
$app->router->group([
    'namespace' => 'app\\controller',
    'interceptor' => [
        '\app\interceptor\ErrorHandler::class',
    ],
], function (\spaphp\routing\Router $router) {
    $router->get('/version', function () use ($router) {
        return $router->app->version();
    });
});
```

更详细的内容请见 **路由篇**

## 4. 执行用户请求

经过上面所介绍的启动流程后，用户就得到一个设置好的Application类对象了，还记得index.php文件最后有一句调用run方法么

```php
$app->run()
```

这里才是真正开始执行用户请求的地方，下面看下run方法都做了哪些工作

```php
public function run(Request $request = null, Response $response = null)
{
    $response = $this->dispatch($request, $response);

    if ($response instanceof Response) {
        $response->send();
    } elseif (is_array($response)) {
        echo json_encode($response);
    } else {
        echo (string)$response;
    }
}
```

看到run方法其实是调用dispatch方法，拿到$response对象并转化为json对象或是字符串返回，处理流程大部分在dispatch方法当中

```php
protected function dispatch(Request $request = null, Response $response = null)
{
    ...
    /**
     * @var Route $route
     */
    // 1. 获取请求路由
    $route = $router->dispatch($request->getMethod(), $request->getPathInfo());
    $request->currentRoute($route);
    $attributes = $route->getAttributes();
    $request->add($attributes);
    // 2. 路由执行方法与参数
    $routeInfo = [
        $route->getAction(),
        $attributes,
    ];
    // 3. 路由执行方法与拦截器一起包装并执行
    $interceptor = array_unique(array_merge($this->interceptor, $route->getInterceptor()));
    if (count($interceptor) > 0) {
        $response = (new PipeLine($this))
            ->send($request)
            ->through($interceptor)
            ->then(function ($request) use ($routeInfo) {
                return $this->runRoute($routeInfo); // <---- 这里执行的是路由方法
            });
    } else {
        $response = $this->runRoute($routeInfo);
    }

    return $response;
}
```

首先这里给出示例对应的路由表如下

```php
array (
  'GET/version' =>
  array (
    0 => 'GET',
    1 => '/version',
    2 =>
    array (
      0 =>
      function() {
        return "spaphp framework v2.0";
      }
    ),
  ),
```

dispatch方法可以分为以下3个步骤

1. 根据请求方法和路径获取请求路由，比如示例中的就是 "GET" 和 "/version"
2. 从请求路由当中取出要执行的方法与参数，示例中就是function(){...}这个闭包函数
3. 路由执行方法与拦截器包装组成最终执行流，这里有些复杂，会在 **拦截器篇** 中详细介绍 

到这里，一个完整的请求流程就已经走完了!!!
