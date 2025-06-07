<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use App\Library\Crud\AdminRoutes;

class AdminRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */

    protected $routes = [];
    protected $dataMetrics= [];

    public function register()
    {
        $this->app->singleton('adminroutes', function ($app) {
            $adminRoutes = new AdminRoutes();
            $adminRoutes::init_routes();
            $adminRoutes::$routeMenu;
            $adminRoutes::$dataGraficMetrics;
            return $adminRoutes;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->routes = $this->app->make('adminroutes')::$routeMenu;
        $this->dataMetrics = $this->app->make('adminroutes')::$dataGraficMetrics;
        $this->menuListSideBar($this->routes);
        $this->dashBoardMetrics($this->dataMetrics);
    }

    
    /**
     * Compartir datos comunes con las vistas de administración.
     *
     * @param array $route
     * @return void
     */
    private function menuListSideBar($routes)
    {
        View::composer('admin.*', function ($view) use ($routes) {
            // Verifica si hay datos de rutas
            if (empty($routes)) {
                Log::warning('No hay rutas definidas en AdminRouteServiceProvider'. json_encode($routes) . ' en ' . __METHOD__);
            }
            $result = [
                'routes' => $routes,
                'nameGrid' => config('appweb.admin.grid.title'),
                'nameForm' => config('appweb.admin.form.title'),
            ];

            $view->with($result);
        });
    }

    /**
     * Compartir métricas del dashboard con la vista de administración.
     *
     * @param array $data
     * @return void
     */
    private function dashBoardMetrics($data)
    {
        View::composer('admin.dashboard', function ($view) use ($data) {
            // Verifica si hay datos de métricas
            if (empty($data)) {
                Log::warning('No se encontraron datos de métricas para el dashboard: ' . json_encode($data) . ' en ' . __METHOD__);
                $data = '{}';
            }

            $result = ['dataMetrics' => is_string($data) ? $data : json_encode($data)];
            $view->with($result);
        });
    }
}