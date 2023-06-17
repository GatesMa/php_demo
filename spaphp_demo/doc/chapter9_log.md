# 日志

<!-- toc -->

## 1. 日志的使用

SPAPHP框架中的日志类是基于 monolog/monolog 组件进行封装的

使用十分便捷，可以用以下方法记录日志

```php
Log::error('log here!!!');
$this->app['log']->error('log here!!!');
App::make(LoggerInterface::class)->error('log here!!!');
App::make('log')->error('log here!!!');
```

这里还是推荐第一种Facade形式的调用

相比于monolog中已有的日志级别，又新增了 ACCESS 和 TRACE 这两种级别

## 2. SPAPHP中日志的实现

首先，如果对于Monolog日志的基本概念不是很熟悉的话，可以先通过 [传送门](https://github.com/Clarence-pan/monolog-zh-doc) 了解一下

SPAPHP中日志类的实现位于 spaphp/framework/src/log 目录下

主要是Logger和LogManager这两个类

其中Logger类继承自Monolog类并实现了自己定义的日志级别(access和trace)

```php
class Logger extends Monolog implements ILogger
{
    /**
     * trace log
     */
    const TRACE = 150;

    /**
     * access log
     * 每次请求，有且仅有一条
     */
    const ACCESS = 700;
    ...
}
```

LogManager类对Logger类又进一步封装，提供了解析配置文件，载入处理器的能力，前面所介绍的几种使用日志的方法，调用的都是LogManager类

```php
class LogManager implements ILogger
{
    protected $config;

    protected $logger;

    public function setConfig(array $config)
    {
        $this->config = $config;
        return $this;
    }

    public function logger()
    {
        if (null === $this->logger) {
            $logger = new Logger($this->config['name']);
            foreach ($this->config['rootLogger'] as $handle) {
                $this->handle($logger, $this->config[$handle]);
            }
            $this->logger = $logger;
        }
        return $this->logger;
    }

    protected function handle(Logger $logger, $handle)
    {
        if ($handle instanceof Closure) {
            return $handle($logger);
        }
        ...
    }

    public function error($message, array $context = [])
    {
        return $this->logger()->error($message, $context);
    }
}
```

通过LogManager类可以看到以下几点

```shell
1. $logger属性是一个Logger类实例
2. LogManager类logger方法解析配置文件，通过配置文件生成处理器，并绑定到$logger中
3. LogManager类中的error方法代理到了$logger属性的error方法上，其他方法类似
```

到这里，又会有疑问，LogManager类在什么时候绑定到容器的呢，其实是在Application类中完成的

```php
protected function loggerBind()
{
    $this->singleton(LoggerInterface::class, function () { // 绑定实现到接口上
        $logger = new LogManager(); 
        $config = $this->loadConfig('logging'); // 读取配置文件
        $logger->setConfig($config); // 设置配置文件到LogManager类中
        return $logger;
    });
}

protected function registerAliases()
{
    $this->aliases = [
        ...
        'log' => LoggerInterface::class, // 创建 'log' 别名
        ...
    ];
}
```

## 3. 自定义日志处理器

通过配置文件可以看到当前已经定义的日志处理器

```php
return [
    'name' => 'logTest',
    'rootLogger' => [  // 这里定义日志处理器的名称
        'single',
        'daily',
    ],
    'single' => [ // 这里对相应名称处理器的参数进行配置
        'handler' => 'single',
        'path' => App::varPath('log/spaphp.log'),
        'level' => 'debug',
    ],
    'daily' => [
        'handler' => 'daily',
        'path' => App::varPath('log/spaphp.log'),
        'level' => 'debug',
        'days' => 7,
    ],
];
```

上面配置文件中已有的两种处理器类型是框架特殊支持的，这两种处理器可以通过配置方式来定义参数。自己新建处理器时，应该按下面这种方式处理

```php
return [
    ...
    'rootLogger' => [
        'spa'
    ],
    'spa' => SPALogger::getHandler(), // 这里'spa' 对应一个Closure对象
];
```

符合原SPA侧日志规范定义的SPALogger::getHandler方法如下

```php
public static function getHandler($level = Logger::DEBUG, $filePermission = 0777)
{
    return function (Logger $logger) use ($level, $filePermission) {
        $handler = new StreamHandler('/data/log/spa-' . date('Y-m-d-H') . '.log', $level, true, $filePermission); // 定义一个StreamHandler处理器对象向文件中写入日志
        $formatArr = [
            'datetime' => '%datetime%',
            'level' => '%level_name%',
            'logger' => '%channel%',
            'line' => '%extra.line%',
            'req_method' => '%context.req_method%',
            'rsp_code' => '%context.rsp_code%',
            'req_url' => '%context.req_url%',
            'req_body' => '%context.req_body%',
            'rsp_body' => '%context.rsp_body%',
            'error_type' => '%context.error_type%',
            //'class' => '%extra.class%',
            //'function' => '%extra.function%',
        ];

        $format = implode("\t", $formatArr) . "\n"; // 定义了日志的格式
        $dateFormat = 'Y-m-d H:i:s.v';
        $formatter = new LineFormatter($format, $dateFormat); // 一条日志写入一行字符串中
        $handler->setFormatter($formatter);

        // Injects line/file:class/function where the log message came from
        $skipClassesPartials = [ // 跳过这些类的调用栈
            'spaphp\\log\\',
            'spaphp\\facade\\',
        ];
        // IntrospectionProcessor加工程序记录下了行数及类名、函数名等额外信息
        $handler->pushProcessor(new IntrospectionProcessor(Logger::DEBUG, $skipClassesPartials));

        $handler->pushProcessor(function ($record) { // 自定义的额外信息
            $record['extra']['uid'] = (int)Request::get('uid', 0);
            $record['extra']['mod_name'] = Request::get('mod', 'unknown');
            $record['extra']['act_name'] = Request::get('act', 'unknown');
            $record['extra']['client'] = Trace::getClient() ?: 'ad_platform';
            $record['extra']['trace_id'] = Trace::getTraceId();
            $record['extra']['span_id'] = Trace::getSpanId();
            $record['extra']['line'] = intval($record['extra']['line'] ?? 0);
            return $record;
        });

        $logger->pushHandler($handler);
    };
}
```

这里的用法都是和Monolog日志相关的，详细内容参考下Monolog的文档即可