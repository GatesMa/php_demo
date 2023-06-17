<?php
/**
 * Created by PhpStorm.
 * User: jomiao
 * Date: 2018/10/25
 * Time: 下午5:59
 */

namespace app\console;

use spaphp\console\Kernel as ConsoleKernel;
use Symfony\Component\Finder\Finder;

class Kernel extends ConsoleKernel
{
    public function loadCommandsFromPath($path = 'console/command', $namespace = 'app\\console\\command')
    {
        $finder = new Finder();
        $finder->files()->in($this->app->path($path));
        foreach ($finder as $file) {
            $file = $file->getRelativePathname();
            $baseName = str_replace('/', '\\', substr($file, 0, -4));
            $className = $namespace . '\\' . $baseName;

            $this->loadCommandFromClass($className);
        }
    }

    public function loadCommandFromClass($class)
    {
        $command = new $class($this);
        $this->add($command);
    }
}
