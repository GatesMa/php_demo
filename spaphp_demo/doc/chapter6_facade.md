# Facade调用

<!-- toc -->

## 1. Facade是什么

Facade为容器当中的类提供了一种静态调用的形式，但是它本质上还是动态的方法调用，Facade方法调用便于编写与测试

下面是SPAPHP框架中Facade调用的几个例子

```php
App::make('request');
Router::get('/', 'IndexController@index');
Log::error('hello');
```

## 2. 项目中定义自己的Facade类

如果在项目当中定义了一个Class A

```php
namespace app\controller;
class A
{
    public function hello()
    {
        return 'hello';
    }
}
```

如何在项目当中为A类定义一个Facade类呢，如下

```php
namespace app\facade;
use app\controller\A; // <---- 这里引用的是controller A
class A extends Facade
{
    public static function accessor()
    {
        return A::class;
    }
}
```

这里看到定义很简单，只要继承自框架的Facade类，并实现accessor方法即可

accessor方法返回的是这个Facade类所要表示的类的名称

## 3. Facade在框架中的实现

框架实现Facade功能的代码在 vendor/spaphp/framework/src/facade/Facade.php 类当中

```php
abstract class Facade
{
    protected static $app;

    protected static $fired = [];

    public static function setApplication($app)
    {
        static::$app = $app;
    }

    public static function clear()
    {
        static::$fired = [];
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::fire(static::accessor());
        return $instance->$method(...$args);
    }

    protected static function fire($name)
    {
        if (is_object($name)) {
            return $name;
        }
        if (isset(static::$fired[$name])) {
            return static::$fired[$name];
        }
        return static::$fired[$name] = static::$app[$name];
    }
}
```

用第二节中的定义的类来说明Facade调用流程

1. 调用 app\facade\A::hello()方法
2. 继承自Facade类的A类当中没有hello这个方法，所以触发了__callStatic方法调用
3. Facade::fire(static::accessor())方法调用
4. A类实现了accessor方法，该方法返回A::class
5. static::$app[A::class]方法调用
6. $app属性是在SPAPHP启动时设置上的Application对象，所以$app[A::class]相当于$app->make(A::class);

在Facade类中还可以看到，$fired属性保存已经创建过的实例，保证了Facade类的调用为单例
