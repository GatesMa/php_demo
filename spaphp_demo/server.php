<?php

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

if ($uri !== '/' && file_exists($file = __DIR__ . '/src/web' . $uri)) {
    readfile($file);
    return;
}

require_once __DIR__ . '/src/web/index.php';
