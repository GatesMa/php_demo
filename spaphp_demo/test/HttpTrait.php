<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2019/1/25
 * Time: 下午3:51
 */

namespace app;

use spaphp\Application;
use spaphp\http\Request;
use spaphp\http\Response;

trait HttpTrait
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @param $uri
     * @param array $headers
     * @return Response
     */
    public function get($uri, array $headers = [])
    {
        $server = $this->headers2Server($headers);
        return $this->call($uri, 'GET', [], [], [], $server);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return Response
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        $server = $this->headers2Server($headers);
        return $this->call($uri, 'POST', $data, [], [], $server);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return Response
     */
    public function put($uri, array $data = [], array $headers = [])
    {
        $server = $this->headers2Server($headers);
        return $this->call($uri, 'PUT', $data, [], [], $server);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return Response
     */
    public function patch($uri, array $data = [], array $headers = [])
    {
        $server = $this->headers2Server($headers);
        return $this->call($uri, 'PATCH', $data, [], [], $server);
    }

    /**
     * @param $uri
     * @param array $data
     * @param array $headers
     * @return Response
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        $server = $this->headers2Server($headers);
        return $this->call($uri, 'DELETE', $data, [], [], $server);
    }

    /**
     * @param $uri
     * @param string $method
     * @param array $data
     * @param array $cookies
     * @param array $files
     * @param array $server
     * @param null $content
     * @return Response
     */
    public function call($uri, $method = 'GET', $data = array(), $cookies = array(), $files = array(), $server = array(), $content = null)
    {
        $request = Request::create($uri, $method, $data, $cookies, $files, $server, $content);
        $response = $this->app->handle($request, null, true);

        return $response;
    }

    protected function headers2server(array $headers)
    {
        $server = [];
        foreach ($headers as $key => $val) {
            $key = str_replace('-', '_', strtoupper($key));
            if ($key != 'REMOTE_ADDR' && $key != 'CONTENT_TYPE' && substr($key, 0, 5) != 'HTTP_') {
                $key = 'HTTP_' . $key;
            }
            $server[$key] = $val;
        }

        return $server;
    }
}