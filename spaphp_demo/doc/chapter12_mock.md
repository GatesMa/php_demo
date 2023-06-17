# Mock

<!-- toc -->

## 1. Mock功能介绍

Mock功能可以在后端没有完成开发之前，模拟真实接口返回的数据，实现前后端的分离开发

SPA-PHP框架的Mock数据基于文档注释生成

首先我们要写好待开发的接口的文档注释，如下

```php
/**
 * Class PhotoController
 * @package app\controller
 * @api
 */
class PhotoController extends Controller
{
    /**
     * @return string
     */
    public function index()
    {
    }
}
```

接下来可以看到启动文件 app.php 中 **已经配置** 好了这样的路由规则

```php
// app routes
$app->router->group([
    'interceptor' => [
        'validator',
    ],
], function (\spaphp\routing\Router $router) {
    $router->get("/photo", "app\\controller\\PhotoController@index"); // <--- 需要被mock的接口
});

$app->router->group([
    'namespace' => 'mock\\controller',
], function (\spaphp\routing\Router $router) {
    $router->any('/mock/{path:.*}', 'MockController'); // <--- mock的路由配置
    $router->get('/swagger.json', 'SwaggerController');
});
```

最后，根据上面配置好的路径，启动项目后，访问

```php
http://localhost:8080/mock/photo
```

看到mock数据成功返回

```php
{
    "ret":0,
    "errors":[],
    "data":"kqVnaO3uk"
}
```

## 2. Mock实现原理

在实现原理这，我们解释一下上面所给的例子中数据是如何返回的

在访问以下路径时

```php
http://localhost:8080/mock/photo
```

根据已配置的路由规则，找到 mock/controller/MockController@__invoke 方法

```php
$router->any('/mock/{path:.*}', 'MockController'); // <--- mock的路由配置
```

这里注意，我们配置的时候只指定到了MockController, 那么如何知道调用它的__invoke方法的呢

其实是因为在 RouteRequest 类的 runRoute 方法中有这样一句

```php
if (!strpos($action, '@')) {
    $action .= '@__invoke';
}
```

就是说当没有指定执行的方法时，默认执行控制器的__invoke方法！！！

现在再看下MockController的__invoke方法

```php
class MockController extends Controller
{
    public function __invoke(string $path)
    {
        // 1. 路由分发
        $httpMethod = $this->app->request->getMethod();
        $uri = '/' . $path;
        $route = $this->app->router->dispatch($httpMethod, $uri);
        $action = $route->getAction();
        $vars = $route->getAttributes();
        $request = $this->app->request;
        $request->add($vars);
        if ($action instanceof \Closure) {
            throw new \RuntimeException("mock server is not support Closure route");
        }

        list($target, $method) = explode('@', $action);

        // 2. 根据文档注释检验对输入参数进行检验
        ......
        // 3. 根据文档注释生成返回参数
        ......

        // 4. 返回数据到前端
        $ret = [
            'ret' => count($errors) > 0 ? 1 : 0,
            'errors' => $errors,
            'data' => $return,
        ];

        return $ret;
    }
}
```

__invoke方法的处理可以分为四步

1. 路由分发中，取得路径 /photo ,并根据配置的路由信息，找到处理方法 PhotoController@index
2. 根据 PhotoController@index 方法文档检验输入参数
3. 根据 PhotoController@index 方法文档Mock出返回数据
4. 返回数据到前端

到这里，Mock功能的实现就已经讲完了~~~
