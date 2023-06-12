# Swagger文档

<!-- toc -->

SPA-PHP可以根据项目中的文档注释生成swagger.json，搭配Swagger UI使用，生成接口说明文档

## 1. 生成项目的Swagger文档

在spa-php项目的根目录下执行如下命令

```
php -S localhost:8080 -t src/web
```

浏览器中打开页面 http://localhost:8080/index.php/swagger.json

可以看到生成的swagger.json文件内容

```
{
    "swagger": "2.0",
    "info": {
        "title": "spaphp",
        "description": "This is a spaphp application",
        "version": "v1.1"
    },
    "host": "localhost:8080",
    "basePath": "/mock",
    "schemes": [
        "http",
        "https"
    ],
    ...
}
```

将文件存储为swagger.json，保存到项目 src/web 路径下

接下来clone一份swagger ui的代码

```
git clone https://github.com/swagger-api/swagger-ui.git
```

把swagger ui项目的 dist 目录下所有内容拷贝到 spa-php 项目的 src/web 路径下

再修改下index.html文件中引用的swagger.json的路径

```
const ui = SwaggerUIBundle({
    //url: "https://petstore.swagger.io/v2/swagger.json",  // <--- 这里注释掉
    url: "./swagger.json", // <--- 使用上面生成的swagger.json路径
    dom_id: '#swagger-ui',
    deepLinking: true,
    presets: [
      SwaggerUIBundle.presets.apis,
      SwaggerUIStandalonePreset
    ],
    plugins: [
      SwaggerUIBundle.plugins.DownloadUrl
    ],
    layout: "StandaloneLayout"
})
```

最后，访问 http://localhost:8080/index.html 路径

就可以看到Swagger文档内容了

<img src="img/swagger/swagger_1.png" height="400" width="700" />


## 2. Swagger文档生成原理（待补充）