<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/11/1
 * Time: 上午11:20
 */

use spaphp\console\ArgvInput;
use spaphp\console\ConsoleOutput;
use app\console\Kernel;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @var \spaphp\Application $app
 */
$app = require __DIR__ . '/../src/bootstrap/app.php';

/**
 * @var Kernel $kernel
 */
$kernel = $app->make(Kernel::class);

/**
 * load commands
 */
$kernel->loadCommandsFromPath(
    $app->config->get('app.command.path'),
    $app->config->get('app.command.namespace')
);

exit($kernel->run(new ArgvInput(), new ConsoleOutput()));
