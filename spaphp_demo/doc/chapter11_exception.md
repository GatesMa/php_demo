# 异常与错误处理

<!-- toc -->

完善的异常与错误处理可以让程序健壮，同时在生产环境中不会暴露过多的信息，对用户提示更加友好

## 1. PHP中异常与错误处理函数

这里先介绍几个PHP中常用的异常与错误处理的函数

### 1.1 error_reporting

error_reporting可以用来设置错误报告级别，也可以获取当前错误报告级别

你可以用这样的方式来暂时改变一下报告级别

$old = error_reporting(-1);
//do something
...
error_reporting($old);

### 1.2 set_error_handler

这个函数用来设置错误发生时的处理方法，但是它不能用来捕获E_ERROR等严重错误

通常与restore_error_handler搭配

### 1.3 set_exception_handler

这个函数用来设置异常处理的方法，一般用来捕获项目中没有得到处理的异常

常与restore_exception_handler搭配

### 1.4 register_shutdown_function

这个函数注册在php程序中止时会进行的处理方法


## 2. SPAPHP中的异常与错误处理

SPAPHP中异常与错误处理是在RegisterErrorHandler这个trait中实现的，通过Application的构造器中调用

```php
$this->registerErrorHandler();
```

进入到registerErrorHandler方法

```php
protected function registerErrorHandler()
{
    error_reporting(-1); // 设置所有级别的错误都进行报告

    set_error_handler(function ($level, $message, $file = '', $line = 0) {
        if (error_reporting() & $level) { // 把捕获到的错误转化为异常
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    });

    set_exception_handler(function ($e) { // 所有项目中没有自行处理的异常都用handleUncaughtException来处理
        $this->handleUncaughtException($e);
    });

    register_shutdown_function(function () { // php退出时捕获严重级别的错误转化为异常信息，前面说了，set_error_handler捕获不到E_ERROR等错误
        if (!is_null($error = error_get_last()) &&  $this->isFatal($error['type'])) {
            $this->handleUncaughtException(new ErrorException(
                $error['message'],
                $error['type'],
                0,
                $error['file'],
                $error['line']
            ));
        }
    });
}
```

再看下handleUncaughtException中的实现

```php
protected function handleUncaughtException($e, $httpMode = false)
{
    $handler = $this->getExceptionHandler();

    if ($e instanceof \Error) { // 错误转化为异常
        $e = $this->transError2Exception($e);
    }

    $handler->log($e); // 记录下异常日志

    if (!$httpMode && $this->isCli()) { // 是否为cli模式
        $handler->handleForConsole($e);
    } else { // http模式
        return $handler->handle($e); 
    }
}
```

这里区分了命令行与Http访问两种模式来处理，在getExceptionHandler方法中会判断用户是否绑定过异常处理器，如果没有绑定，则调用默认的异常处理器

```php
protected function getExceptionHandler()
{
    if ($this->bound(ExceptionHandler::class)) {
        return $this->make(ExceptionHandler::class);
    } else {
        return $this->make(DefaultExceptionHandler::class);
    }
}
```

从上面也可以看出，如果用户要定义自己的异常处理器，需要 **实现自ExceptionHandler接口，然后绑定到容器当中**



