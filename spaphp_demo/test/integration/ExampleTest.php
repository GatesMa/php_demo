<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2019/1/25
 * Time: 下午3:02
 */

namespace app\integration;


use app\TestCase;

class ExampleTest extends TestCase
{

    public function testBasic()
    {
        $response = $this->get('/');
        $this->assertEquals(200, $response->getStatusCode());
    }
}
