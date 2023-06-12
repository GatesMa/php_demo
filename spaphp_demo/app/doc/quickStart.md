# 安装配置

<!-- toc -->

## 1. 运行环境要求

- PHP >= 7.1.3
- PHP OpenSSL
- PHP Mbstring
- PHP JSON

## 2. 基于SPAPHP开发项目

SPAPHP使用composer管理依赖，所以安装SPAPHP之前需确保已经安装了composer。

### 2.1 配置composer镜像资源地址

编辑composer的全局配置文件config.json文件内容如下 (文件位于 ~/.composer/ 目录下)

```
{
    "config": {
        "secure-http": false
    },
    "repositories": {
        "packagist": {
            "type": "composer",
            "url": "http://packagist.tsa.oa.com"
        }
    }
}
```

配置文件指定了镜像路径，并允许http的方式下载

### 2.2 引入安装器

通过如下命令引入安装器组件

```php
composer global require spaphp/installer:dev-master
```

### 2.3 配置PATH路径

执行如下语句把composer的可执行文件路径加入到PATH中

```
export PATH=$HOME/.composer/vendor/bin:$PATH
```


### 2.4 执行安装命令

最后再执行如下语句

```php
spaphp new app
```

这样就会在当前目录下生成一个名为app的应用了

## 3. WEB服务器配置

### 3.1 PHP内置Server

使用如下命令开启 php 内置的 webserver 功能，即可在浏览器中访问 http://localhost:8090/

```shell
php -S localhost:8090  server.php
```
### 3.2 Apache配置

使用框架自带的 src/web/.htaccess 文件支持 url rewrite

### 3.3 Nginx配置

```shell
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

一个简单的nginx配置范例

```shell
server {
    listen 80;
    server_name example.com;
    root /example.com/src/web;
    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        include fastcgi_params;
    }
    
    # deny acccess to .htaccess file
    location ~ /\.ht {
        deny all;
    }
}
```

## 4. 目录结构

**src/bootstap**

bootstrap 目录包含用户框架启动和自动载入的一些文件

**src/console**

console 目录包含了命令行相关的文件

> 推荐以在 src/console/command 目录中扩充命令

**src/controller**

controller 目录包含了web服务的控制器文件

> 推荐在 src/controller 目录扩充控制器

**src/etc**

etc 目录包含了所有的配置文件

**src/interceptor**

interceptor 目录包含了拦截器文件

**src/model**

model 目录包含了模型定义文件

**src/provider**

provider 目录包含了服务提供者文件

**src/routes**

routes 目录包含了应用定义的路由。

web.php 文件包含了手动自定义的路由

cached_routes.php 文件包含了通过注解自动生成的路由


**src/var**

var 目录包含了生成的缓存文件和日志文件


**src/web**

web 目录包含了应用入口文件 inddex.php 和前端资源文件，该目录是 web服务器的应用根目录

**bin**

bin 目录包含了 spaphp 命令行的运行脚本

**mock**

mock 目录包含了 swagger ui 所使用的 swagger.json 和 mock 服务


**doc**

doc 目录为 spaphp 文档目录