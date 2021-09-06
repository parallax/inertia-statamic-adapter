<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\Inertia;
use JsonSerializable;
use Statamic\Entries\Entry;
use Statamic\Facades\GlobalSet as GlobalSetFacade;
use Statamic\Fields\Value;
use Statamic\Globals\GlobalCollection;
use Statamic\Globals\GlobalSet;
use Statamic\Http\Controllers\FrontendController;
use Statamic\Structures\Page;

class InertiaStatamicAdapter
{
    protected $data;

    public function handle(Request $request, Closure $next)
    {
        $page = app(FrontendController::class)->index($request);

        $this->data = $page->toAugmentedArray();

        if ($page instanceof Page || $page instanceof Entry) {
            return Inertia::render(
                $this->buildComponentPath($page),
                $this->buildProps($page),
            );
        }

        return $next($request);
    }

    protected function buildComponentPath()
    {        
        $collection = $this->data['collection']->toAugmentedArray();
        $blueprint = $this->data['blueprint']->toAugmentedArray();

        $path = collect([
            $collection['handle'],
            $blueprint['handle'],
        ]);

        $path = $path->map(function ($item) {
            return Str::studly($item);
        });

        return $path->join('/');
    }

    protected function buildProps()
    {
        return [
            'entry' => $this->buildEntryProps(),
            'globals' => $this->buildGlobalProps(),
        ];
    }

    protected function buildEntryProps()
    {
        return $this->formatPropData($this->data);
    }

    protected function buildGlobalProps()
    {
        $globals = GlobalSetFacade::all();

        return collect($globals)->mapWithKeys(function ($global) {
            return [$global->handle() => $this->formatPropData($global)];
        })->toArray();
    }

    protected function formatPropData($data)
    {
        if ($data instanceof Carbon) {
            return $data;
        }

        if ($data instanceof JsonSerializable || $data instanceof Collection) {
            return $this->formatPropData($data->jsonSerialize());
        }

        if ($data instanceof GlobalSet) {
            return $this->formatPropData($data->localizations()->get('default'));
        }

        if (is_array($data)) {
            return collect($data)->map(function ($value) {
                return $this->formatPropData($value);
            })->all();
        }

        if ($data instanceof Value) {
            return $data->value();
        }

        if (is_object($data) && method_exists($data, 'toAugmentedArray')) {
            return $this->formatPropData($data->toAugmentedArray());
        }

        return $data;
    }
}
