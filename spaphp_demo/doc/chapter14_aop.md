# SPAPHP AOP

## 1. 使用介绍

SPAPHP提供了一个简单易用的AOP(面向切面编程)库，借此你可以很方便的扩展原有的功能

当前SPAPHP AOP的功能特性如下

    1. 支持对类的非final、非静态的public方法进行代理
    2. 支持方法当中引用参数的传递
    3. 支持方法可变参数的写法

下面以实例说明如何使用AOP功能

假如原有一个A类定义如下

```php
class A
{
    public function sayHello($name)
    {
        return 'hello to'. $name;
    }
}
```

现在我们想在A类的sayHello方法执行前后分别添加其他的工作，可以这样写

```php
$object = new A();
$aop = new Proxy(); // 创建代理类对象
$proxy_class_name = $aop->load($object); // 对原来的A类实例进行代理并返回代理类名称
$proxy = new $proxy_class_name(); // 代理类实例化

// 定义自己的代理逻辑
/**
 * @param $originalFunc  <-- 表示被代理的类方法的闭包
 * @param $name <-- 被代理的函数名(即sayHello)
 * @param mixed ...$args <-- 参数列表，支持可变参数的写法
 */
$callback = function($originalFunc, $name, ...$args) {
    echo $name. '方法已经被代理'. PHP_EOL;
    echo 'before'. PHP_EOL;
    $ret = call_user_func($originalFunc, ...$args); // 这句表示执行原来类A的sayHello方法
    echo $ret. PHP_EOL;
    echo 'after'. PHP_EOL;
}

// 将自定义的代理逻辑设置到代理类当中
$proxy->setAopProxyCallback($callback);
// 代理类执行sayHello方法
$proxy->sayHello('alice');
```

执行上面这段代码，输出如下

```shell
sayHello方法已经被代理
before
hello to alice
after
```

接下来，一起看下代理类的背后都发生了什么吧~~~

## 2. 实现原理

SPAPHP AOP的实现源码在spaphp/framework/src/aop/Proxy.php当中

先来看下load函数的逻辑吧

```php
public function load($object)
{
    $refl = new \ReflectionClass($object);
    // 如果原有类是final的或者不能实例化，直接返回原有类名，不进行代理
    if ($refl->isFinal() || !$refl->isInstantiable()) {
        return $refl->getName();
    }
    $className = $refl->getName();
    // 生成代理类的名称
    $proxyName = $this->prefix . '_' . str_replace('\\', '_', $className);
    // 如果这个代理类没有加载过，则动态生成代理类的代码并加载
    if (!class_exists($proxyName, false)) {
        // 动态生成代理类代码的逻辑
        $code = $this->getClassCode($refl, $className, $proxyName);
        // 利用eval函数动态加载代理类，如果对于php eval函数不是很熟悉的话，参考https://www.php.net/manual/zh/function.eval.php
        eval('?>' . trim($code));
    }
    return $proxyName;
}
```

接下来，看下getClassCode方法是怎么做的

```php
protected function getClassCode(\ReflectionClass $class, $className, $proxyName): string
{
    // 代理类的模板
        $tpl = <<<'EOD'
<?php
class %s extends %s implements \spaphp\aop\IProxyObject
{
    /**
     * @var Closure
     */
    protected $aopProxyCallback;
    /**
     * @return Closure
     */
    public function aopProxyCallback()
    {
        return $this->aopProxyCallback;
    }
    /**
     * @param Closure $aopProxyCallback
     * @return \spaphp\aop\IProxyObject
     */
    public function setAopProxyCallback(Closure $aopProxyCallback)
    {
        $this->aopProxyCallback = $aopProxyCallback;
        return $this;
    }
%s
}
EOD;
        // 解析原有类结构，生成代理方法的代码
        $methodsCode = $this->getMethodsCode($class);
        // 填充代理类模板
        $code = sprintf($tpl, $proxyName, $className, $methodsCode);
        return $code;
    }
}
```

可以看到，AOP的核心实现逻辑就是 **根据原有类的结构，生成一个继承自原有类的代理类，并通过eval函数把这个代理类动态加载进来**

最后，将前面介绍的使用实例中所生成的代理类打印出来，具体看下

```php
class SpaAopProxy_spaphp_aop_A extends spaphp\\aop\\A implements \\spaphp\\aop\\IProxyObject
{
    /**
     * @var Closure
     */
    protected $aopProxyCallback;

    /**
     * @return Closure
     */
    public function aopProxyCallback()
    {
        return $this->aopProxyCallback;
    }

    /**
     * @param Closure $aopProxyCallback
     * @return \\spaphp\\aop\\IProxyObject
     */
    public function setAopProxyCallback(Closure $aopProxyCallback)
    {
        $this->aopProxyCallback = $aopProxyCallback;
        return $this;
    }

    public function sayHello($name)
    {
        if ($this->aopProxyCallback) {
            // 调用原有类方法的闭包
            $originalFunc = function($name) {
                return parent::sayHello($name);
            };
            // 从这里可以看到用户自定义的闭包前两个参数所代表的意义了
            return $this->aopProxyCallback()($originalFunc, __FUNCTION__, $name);
        }
        return parent::sayHello($name);
    }
}
```

至此，原理部分也介绍完毕了！
