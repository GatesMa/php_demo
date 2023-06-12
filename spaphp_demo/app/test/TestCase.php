<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2019/1/25
 * Time: 下午2:49
 */

namespace app;

use PHPUnit\Framework\TestCase as BaseTestCase;
use spaphp\facade\Facade;

/**
 * Class TestCase
 * @package app
 */
abstract class TestCase extends BaseTestCase
{
    use HttpTrait;

    protected function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        if (!$this->app) {
            $app = require __DIR__ . '/../src/bootstrap/app.php';
            $this->app = $app;
        }

        Facade::clear();
    }

    protected function tearDown()/* The :void return type declaration that should be here would cause a BC issue */
    {
        if ($this->app) {
            $this->app->flush();
            $this->app = null;
        }
    }

}
