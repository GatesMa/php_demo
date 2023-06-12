# spaphp V1.1 文档

## 特点

- 强大的服务容器
- 完善的执行流程
- 灵活的干预机制
- 灵活的路由规则
- 丰富的组件支持
- 集成数据校验
- 集成数据mock
- 集成文档(swagger或IDL)
- 丰富的工具命令（代码自动生成、路由规则自动生成等）

### PSR-2

spaphp 默认采用 PSR-2 代码风格

使用 phpcs 检查
```
./vendor/bin/phpcs -p --standard=PSR2 --ignore=vendor ./src

```

修正
```
./vendor/bin/phpcbf -p --standard=PSR2 --ignore=vendor ./src

```

### 版本号

遵循 [语义化版本 2.0.0](https://semver.org/lang/zh-CN/)

## 快速入门

### [安装配置](quickStart.md)

### [执行流程](application.md)

## 框架基础

### [服务容器](chapter1_container.md)

### [路由](chapter4_router.md)

### [拦截器](chapter3_interceptor.md)

### [控制器](chapter5_controller.md)

### [Facade](chapter6_facade.md)

### [请求与返回](chapter7_request.md)

### [命令行](chapter8_console.md)

### [校验](chapter10_validate.md)

### [异常与错误处理](chapter11_exception.md)

### [日志](chapter9_log.md)


## 框架进阶

### [Mock功能](chapter12_mock.md)

### [Swagger文档](chapter13_swagger.md)

### CORS 配置

CORS 是一个W3C标准，全称是"跨域资源共享"（Cross-origin resource sharing），就是其中一种用来解决浏览器跨域的问题方法。

> app.cors 中配置


配置 |	对应的 Header |	说明
--- | --- | ---
allowedOrigins |	Access-Control-Allow-Origin |	允许的域名
allowedMethods |	Access-Control-Allow-Methods |	允许的 HTTP 方法
allowedHeaders |	Access-Control-Allow-Headers |	允许的 Header
supportsCredentials	| Access-Control-Allow-Credentials |	是否携带 Cookie
maxAge |	Access-Control-Max-Age |	预检请求的有效期
exposedHeaders |	Access-Control-Expose-Headers |	除了 6 个基本的头字段，额外允许的字段


### 缓存

基于 psr-16 规范实现
1. 基于文件的缓存 FileCache
2. 黑洞缓存 NullCache


### 事件驱动，非阻塞 IO
> event-driven, non-blocking IO

```
$event = Factory::create();
$event->loop();
```


### process

多进程任务模型


### socket

支持自定义协议的 socket server


### [websocket](https://tools.ietf.org/html/draft-ietf-hybi-thewebsocketprotocol-17)

websocket server


### web server

just a pure php web server


### spa web server

针对 spaphp 定制的 http server


### spa websocket server

针对 spaphp 定制的 websocket server


### 辅助


## 应用测试

### 快速入门



### HTTP集成测试



### 命令行测试

