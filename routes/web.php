<?php
use Binafy\LaravelUserMonitoring\Controllers\ActionMonitoringController;
use Binafy\LaravelUserMonitoring\Controllers\AuthenticationMonitoringController;
use Binafy\LaravelUserMonitoring\Controllers\VisitMonitoringController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|


Route::prefix('admin/grid/user-monitoring')->as('user-monitoring.')->group(function ($router) {
    // Visit Monitoring
    $router->get('visits-monitoring', [VisitMonitoringController::class, 'index'])->name('visits-monitoring');
    $router->delete('visits-monitoring/{visitMonitoring}', [VisitMonitoringController::class, 'destroy'])->name('visits-monitoring-delete');

    // Action Monitoring
    $router->get('actions-monitoring', [ActionMonitoringController::class, 'index'])->name('actions-monitoring');
    $router->delete('actions-monitoring/{actionMonitoring}', [ActionMonitoringController::class, 'destroy'])->name('actions-monitoring-delete');

    // Authentication Monitoring
    $router->get('authentications-monitoring', [AuthenticationMonitoringController::class, 'index'])->name('authentications-monitoring');
    $router->delete('authentications-monitoring/{authenticationMonitoring}', [AuthenticationMonitoringController::class, 'destroy'])->name('authentications-monitoring-delete');
});
*/
Route::prefix('auth')->group(function () {
    Auth::routes();
});

Route::get('/admin/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('admin');
