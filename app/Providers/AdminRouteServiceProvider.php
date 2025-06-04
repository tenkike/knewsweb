<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Library\Crud\AdminRoutes;

class AdminRouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('adminroutes', function ($app) {
            return new AdminRoutes(); // No es necesario usar la barra invertida para instanciar la clase
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Inyectar 'adminroutes' en lugar de usar app('adminroutes')
        $AdminRoutes = $this->app->make('adminroutes');

        // Inicializar las rutas
        $AdminRoutes::init_routes();

        // Obtener las rutas y datos de esquema
        $this->routes = $AdminRoutes->routeMenu;
        $this->getShemaData = $AdminRoutes::$schemaDataTable;

        // Compartir datos con las vistas
        $this->shareAdminData($this->routes);
        $this->shareDashboardData($this->getShemaData);
    }

    /**
     * Compartir datos comunes con las vistas de administración.
     *
     * @param array $route
     * @return void
     */
    private function shareAdminData($route)
    {
        View::composer('admin.*', function ($view) use ($route) {
            $result = [
                'routes' => $route,
                'nameGrid' => config('appweb.admin.grid.title'),
                'nameForm' => config('appweb.admin.form.title'),
            ];

            $view->with($result);
        });
    }

    /**
     * Compartir datos del dashboard con la vista de administración.
     *
     * @param array $data
     * @return void
     */
    private function shareDashboardData($data)
    {
        View::composer('admin.dashboard', function ($view) use ($data) {
            $result = ['getShemaData' => $data];
            $view->with($result);
        });
    }
}