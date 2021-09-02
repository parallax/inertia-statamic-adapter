<?php

namespace Parallax\InertiaStatamicAdapter\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Parallax\InertiaStatamicAdapter\Middleware\InertiaStatamicAdapter;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->app[Kernel::class]->appendMiddlewareToGroup('web', InertiaStatamicAdapter::class);
    }
}
