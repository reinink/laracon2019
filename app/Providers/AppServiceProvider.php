<?php

namespace App\Providers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupPagination();

    }

    protected function setupPagination()
    {
        Paginator::defaultView('pagination');

        Collection::macro('paginate', function ($perPage = 15) {
            $paginator = new LengthAwarePaginator(
                $this->forPage(LengthAwarePaginator::resolveCurrentPage(), $perPage),
                $this->count(),
                $perPage
            );

            $paginator->setPath(\Illuminate\Support\Facades\Request::path());

            return $paginator;
        });
    }
}
