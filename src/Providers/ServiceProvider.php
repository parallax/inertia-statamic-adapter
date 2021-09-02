<?php

namespace Parallax\InertiaStatamicAdapter\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->app[Kernel::class]->appendMiddlewareToGroup('web', InertiaStatamicAdapter::class);
    }
}
