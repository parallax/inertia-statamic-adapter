<?php

namespace Parallax\InertiaStatamicAdapter\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use JsonSerializable;
use Statamic\Entries\Entry;
use Statamic\Fields\Value;
use Statamic\Http\Controllers\FrontendController;
use Statamic\Structures\Page;

class InertiaStatamicAdapter
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Inertia\Response|mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $page = app(FrontendController::class)->index($request);

        if ($page instanceof Page || $page instanceof Entry) {
            return Inertia::render(
                $this->componentPath($page),
                $this->props($page)
            );
        }

        return $next($request);
    }

    protected function componentPath($data)
    {
        $values = $data->toAugmentedArray();
        
        $collection = $values['collection']->toAugmentedArray();
        $blueprint = $values['blueprint']->toAugmentedArray();

        $path = collect([
            $collection['handle'],
            $blueprint['handle'],
        ]);

        $path = $path->map(function ($item) {
            return Str::studly($item);
        });

        return $path->join('/');
    }

    protected function props($data)
    {
        if ($data instanceof Carbon) {
            return $data;
        }

        if ($data instanceof JsonSerializable || $data instanceof Collection) {
            return $this->buildProps($data->jsonSerialize());
        }

        if (is_array($data)) {
            return collect($data)->map(function ($value) {
                return $this->buildProps($value);
            })->all();
        }

        if ($data instanceof Value) {
            return $data->value();
        }

        if (is_object($data) && method_exists($data, 'toAugmentedArray')) {
            return $this->buildProps($data->toAugmentedArray());
        }

        return $data;
    }
}
