<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/4/12
 * Time: 下午5:43
 */


return [
    'name' => 'spaphp',
    'description' => 'This is a spaphp application',
    'version' => 'v1.1',
    'mock_server' => '/mock',

    /**
     * command 配置
     */
    'command' => [
        'path' => 'console/command',
        'namespace' => 'app\\console\\command',
    ],

    /**
     * controller 配置
     */
    'controller' => [
        'path' => 'controller',
        'namespace' => 'app\\controller',
    ],

    /**

     * Cross-Origin Resource Sharing
     * @doc https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
     */
    'cors' => [
        'allowedOrigins' => '*', // allow only requests with origins from a whitelist (by default all origins are allowed)
        'allowedHttpMethods' => 'GET, POST, DELETE, PUT, PATCH, OPTIONS', // allow only HTTP methods from a whitelist for preflight requests (by default all methods are allowed)
        'allowedHttpHeaders' => 'Origin, X-Requested-With, Content-Type, Accept', // allow only HTTP headers from a whitelist for preflight requests (by default all headers are allowed)
        'supportsCredentials' => false, //disable/enable support for credentials (by default credentials support is enabled)
        'maxAge' => 3600, // set how long the results of a preflight request can be cached in a preflight result cache (by default 1 hour)
        'exposedHeaders' => 'Access-Control-Allow-Origin', //set custom HTTP headers to be exposed in the response (by default no headers are exposed)
    ],

    'key' => 'YOUR_MIXED_KEY',
    'timezone' => 'Asia/chongqing',
];
