<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
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
        $adminDataRoutes = $this->app->make('adminroutes');
        if (!$adminDataRoutes) {
            Log::error('No se pudo inicializar AdminRoutes en ' . __METHOD__);
            return;
        }
        
        $dbName = $adminDataRoutes::getDatabaseName();
        $routes = $adminDataRoutes::$routeMenu;
        $this->menuListSideBar($routes);

        
        $dataMetrics = Cache::get('schema_tables_metrics_' . $dbName);
        if (empty($dataMetrics)) {
            Log::info('No se encontraron métricas en caché, regenerando para ' . $dbName);
            $tables = $adminDataRoutes::querySchemaTables();
            $adminDataRoutes::processDataMetrics($tables);
            $dataMetrics = Cache::get('schema_tables_metrics_' . $dbName);
        } else {
            Log::info('Obteniendo métricas de caché para ' . $dbName);
        }
        $this->dashBoardMetrics($dataMetrics);
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
            if (empty($data)) {
                Log::warning('No se encontraron datos de métricas para el dashboard: ' . json_encode($data) . ' en ' . __METHOD__);
                $data = '{}';
            }

            $result = ['dataMetrics' => is_string($data) ? $data : json_encode($data ?: new \stdClass())];
            $view->with($result);
        });
    }
}