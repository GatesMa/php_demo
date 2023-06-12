<?php

namespace app\provider;

use spaphp\Application;
use spaphp\cache\FileCache;

class AppServiceProvider
{
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function register()
    {
        // cache
        $this->app->singleton('cache', function() {
            return $this->app->make(FileCache::class, ['directory' => $this->app->varPath('cached')]);
        });
        $this->app->alias('cache', 'Psr\SimpleCache\CacheInterface');
    }

}

