<?php

// 默认开发环境
if (empty($_SERVER['env'])) {
    $_SERVER['env'] = 'develop';
}

$app = new \spaphp\Application(realpath(__DIR__ . '/../'));

// 加载项目全局配置
$app->loadConfig('app');

// register service provider
$app->register(\app\provider\AppServiceProvider::class);
$app->register(\spaphp\metadata\MetadataServiceProvider::class);

// interceptor
$app->interceptor(
    [
        \app\interceptor\ErrorHandler::class,
    ]
);

// route interceptor
$app->routeInterceptor([
    'validator' => 'app\interceptor\ValidatorInterceptor',
]);

// app routes
$app->router->group([
    'interceptor' => [
        'validator',
    ],
], function (\spaphp\routing\Router $router) {
    require __DIR__ . '/../routes/cached_routes.php';
});

// web routes
$app->router->group([
    'namespace' => 'app\\controller',
    'interceptor' => [
        'validator',
    ],
], function (\spaphp\routing\Router $router) {
    require __DIR__ . '/../routes/web.php';
});

// load mock server routes
if ($_SERVER['env'] !== 'formal') {
    if (!$app->isCli() && isset($_SERVER['SERVER_ADDR'])) {
        header('X-SpaServer:' . $_SERVER['SERVER_ADDR'] . ':' . $_SERVER['SERVER_PORT']);
    }

    $app->router->group([
        'namespace' => 'mock\\controller',
    ], function (\spaphp\routing\Router $router) {
        $router->any('/mock/{path:.*}', 'MockController');
        $router->get('/swagger.json', 'SwaggerController');
    });
}

// Cross-Origin Resource Sharing
// https://www.w3.org/TR/cors/
$app->router->options('/{path:.*}', function ($path) use ($app) {
    $headers = [
        'Access-Control-Allow-Origin' => $app['config']->get('app.cors.allowedOrigins'),
        'Access-Control-Allow-Methods' => $app['config']->get('app.cors.allowedHttpMethods'),
        'Access-Control-Allow-Headers' => $app['config']->get('app.cors.allowedHttpHeaders'),
        'Access-Control-Allow-Credentials' => $app['config']->get('app.cors.supportsCredentials'),
        'Access-Control-Max-Age' => $app['config']->get('app.cors.maxAge'),
        'Access-Control-Expose-Headers' => $app['config']->get('app.cors.exposedHeaders'),
    ];
    return new \spaphp\http\Response('', 200, $headers);
});

return $app;
