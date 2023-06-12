# 请求与返回

<!-- toc -->

## 1. 请求与返回对象的生成

请求类用来封装一次请求信息，它的实现位于 spaphp/framework/src/http/Request.php 文件中

返回类用于封装要返回给浏览器的信息，它的实现位于 spaphp/framework/src/http/Response.php 文件中

在框架当中，类实例的生成在 spaphp/framework/src/RouteRequest.php 的dispatch方法当中

```php
protected function dispatch(Request $request = null, Response $response = null)
{
    try {
        $this->bootstrap();
        if ($request === null) {
            $request = Request::getInstance();
        }
        $this->instance('request', $request);
        if ($response === null) {
            $response = Response::create();
        }
        $this->instance('response', $response);
    } catch (Exception $e) {
        ...
    } catch (Throwable $e) {
        ...
    }
}
```

可以看到，在请求分发处理当中，生成了请求类与返回类的对象，并将对象实例绑定到容器当中，这样后续就可以通过容器很方便地访问到请求与返回对象了

再看下 Request::getInstance 方法

```php
public static function getInstance()
{
    if (null === static::$instance) {
        static::enableHttpMethodParameterOverride();
        $request = static::createFromGlobals();
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')
            && $request->getRealMethod() == 'POST'
        ) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace($data);
        }
        static::$instance = $request;
    }
    return static::$instance;
}
```

通过getInstance方法，保证了Request类的单例

其中 enableHttpMethodParameterOverride 方法开启方法参数覆盖，即可以在 POST 请求中添加_method参数来伪造 HTTP 方法（如post中添加_method=DELETE来构造 HTTP DELETE 请求）

Request类继承自symfony组件的Request类，更多的可以去看下symfony中的实现

Response同样继承自symfony组件的Response类，创建过程可以自行阅读

## 2. 请求与返回对象的使用

请求与返回对象的使用十分简单，可以看下 spaphp/framework/src/http/Request 类 与 spaphp/framework/src/http/Response 中提供的方法