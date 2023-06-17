<?php
/**
 * web root
 */

require __DIR__ . '/../../vendor/autoload.php';

/**
 * @var \spaphp\Application $app
 */
$app = require __DIR__ . '/../bootstrap/app.php';

$app->run();
