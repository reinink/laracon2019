<?php

namespace App\Providers;

use Debugbar;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use DebugBar\DataCollector\Renderable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;
use DebugBar\DataCollector\DataCollector;
use Illuminate\Pagination\LengthAwarePaginator;

class AppServiceProvider extends ServiceProvider
{
    public static $hydratedModels = 0;

    /**
     * Register any application services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->registerQueryBuilderMacros();
        $this->setupPagination();
        $this->addHydratedModelMetric();
    }

    protected function registerQueryBuilderMacros()
    {
        Builder::macro('addSubSelect', function ($column, $query) {
            if (is_null($this->columns)) {
                $this->select($this->from.'.*');
            }

            return $this->selectSub($query, $column);
        });

        Builder::macro('orderBySub', function ($query, $direction = 'asc') {
            list($query, $bindings) = $this->createSub($query);

            return $this->addBinding($bindings, 'order')->orderBy(DB::raw('('.$query.')'), $direction);
        });
    }

    protected function setupPagination()
    {
        Paginator::defaultView('pagination');

        Collection::macro('paginate', function ($perPage = 15) {
            return new LengthAwarePaginator(
                $this->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
                $this->count(),
                $perPage
            );
        });
    }

    protected function addHydratedModelMetric()
    {
        Event::listen('eloquent.*', function ($event) {
            if (strpos($event, 'eloquent.retrieved') !== false) {
                AppServiceProvider::$hydratedModels++;
            }
        });

        Debugbar::addCollector(new class() extends DataCollector implements Renderable {
            public function collect()
            {
                return null;
            }

            public function getName()
            {
                return 'models';
            }

            public function getWidgets()
            {
                return [
                    'models' => [
                        'icon' => 'database',
                        'tooltip' => 'Hydrated Models',
                        'map' => '',
                        'default' => "'".AppServiceProvider::$hydratedModels." models'",
                    ],
                ];
            }
        });
    }
}
