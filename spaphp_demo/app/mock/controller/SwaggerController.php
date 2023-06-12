<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/6/29
 * Time: 下午2:19
 */

namespace mock\controller;

use spaphp\swagger\Swagger;
use Symfony\Component\Finder\Finder;

class SwaggerController extends Controller
{
    public function __invoke()
    {
        /**
         * @var Swagger $swagger
         */
        $swagger = $this->app->make(Swagger::class);

        $swagger->swagger->info->title = $this->app->config->get('app.name');
        $swagger->swagger->info->description = $this->app->config->get('app.description');
        $swagger->swagger->info->version = $this->app->config->get('app.version');

        /**
         * @var \spaphp\http\Request $request
         */
        $request = $this->app['request'];
        $swagger->swagger->host = $request->getHttpHost();
        $swagger->swagger->basePath = $this->app->config->get('app.mock_server');

        $finder = new Finder();
        $finder->files()->in($this->app->path($this->app->config->get('app.controller.path')));
        foreach ($finder as $file) {
            $file = $file->getRelativePathname();
            $baseName = str_replace('/', '\\', substr($file, 0, -strlen('.php')));
            $controller = $this->app->config->get('app.controller.namespace') . '\\' . $baseName;
            if (!class_exists($controller)) {
                throw new \InvalidArgumentException('controller [' . $controller . '] not exists');
            }
            $swagger->process($controller);
        }

        $json = $swagger->toJson();
        return $json;
    }
}