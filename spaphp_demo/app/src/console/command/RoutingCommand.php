<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/11/2
 * Time: 下午12:52
 */

namespace app\console\command;

use spaphp\console\contract\Input;
use spaphp\console\contract\Output;
use spaphp\console\Command;
use spaphp\metadata\MetadataFactory;
use Symfony\Component\Finder\Finder;
use spaphp\console\InputArgument;
use spaphp\facade\Config;

/**
 * Class RoutingCommand
 *
 * @package app\command
 *
 * @property MetadataFactory $metadataFactory
 */
class RoutingCommand extends Command
{
    protected function config()
    {
        $this->setName('routing');
        $this->setDefinition([
            new InputArgument(
                'path',
                InputArgument::OPTIONAL,
                '目录',
                Config::get('app.controller.path') ?? 'controller'
            ),
            new InputArgument(
                'namespace',
                InputArgument::OPTIONAL,
                '目录对应的 namespace',
                Config::get('app.controller.namespace') ?? 'app\\controller'
            ),
        ]);

        $this->setDescription('生成路由');
    }

    protected function execute(Input $input, Output $output)
    {
        $routes = $this->getRoutesFromPath($input->getArgument('path'), $input->getArgument('namespace'));

        $content = '<?' . "php\n\n### WARNING: DON'T EDIT THIS FILE ###\n";
        $content .= "### Created at " . date('Y-m-d H:i:s') . " ###\n\n";
        $content .= '/**' . "\n" . ' * @var \spaphp\routing\Router $router' . "\n" . " */\n\n";

        foreach ($routes as $route) {
            $content .= '$router->addRoute("' . $route[0] . '", "' . $route[1] . '", "' . addcslashes($route[2], '\\') . '");' . "\n";
        }

        file_put_contents($this->app->path('routes/cached_routes.php'), $content);
    }

    protected function getRoutesFromPath($path = 'controller', $namespace = 'app\\controller')
    {
        $finder = new Finder();
        $finder->files()->in($this->app->path($path));

        $routes = [];
        foreach ($finder as $file) {
            $file = $file->getRelativePathname();
            $baseName = str_replace('/', '\\', substr($file, 0, -4));
            $className = $namespace . '\\' . $baseName;

            $routes = array_merge($routes, $this->getRoutesFromClass($className));
        }

        return $routes;
    }

    protected function getRoutesFromClass($class)
    {
        $routes = [];
        $this->metadataFactory->setCache();
        $classMetadata = $this->metadataFactory->getClassMetadata($class);

        // 有 api 注解时，才视为启用自动 routing
        if (!$classMetadata->hasTag('api')) {
            return $routes;
        }

        foreach ($classMetadata->methods as $method => $methodMetadata) {
            if ($methodMetadata->getDeclaringClass() !== $methodMetadata->getClass()) {
                continue;
            }
            $route = $methodMetadata->getRoute();
            if (empty($route)) {
                continue;
            }

            list($requestMethod, $path) = explode(' ', $route);
            $routes[] = [
                $requestMethod,
                $path,
                $class . '@' . $method,
            ];
        }

        return $routes;
    }

    public function __get($name)
    {
        return $this->$name = $this->app->$name;
    }

}
