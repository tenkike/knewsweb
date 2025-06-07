<?php
namespace App\Library\Crud;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use App\Library\Crud\DataSchema;

use Illuminate\Support\Facades\Route;

class AdminRoutes extends DataSchema {

	public static $dataMetrics;
	public static $routeMenu = [];

	public function __construct()
	{
		self::_setRoutes();
		//self::$dataMetrics = self::getDataMetrics();
	}

	/**
	 * Inicializa las rutas de administración y las rutas de la API.
	 *
	 * @return void
	 */
	public static function init_routes(){

		// Define las rutas de administración
		Route::middleware(['web', 'auth'])->prefix('admin')->group(function(){
			self::_routes();
		});
		// Define las rutas de administración para la API
		Route::middleware(['api'])->prefix('api/admin')->group(function(){
			self::_routesApi();
		});
		
	}   

	/**
	 * Obtiene las rutas de administración.
	 * 
	 * @return array
	 */
	private static function _routes(){
		// Define las rutas de administración
		$uris=[];
		$data= self::$routeMenu;

		if(!empty($data)){
			foreach($data as $k=> $rows){
				$uris[$k]['grid']= '/grid/'.$k;
				$uris[$k]['create']= '/form/'.$k.'/create';
				$uris[$k]['update']= '/form/'.$k.'/update/{id}';
				$uris[$k]['delete']= $k.'/delete/{id}';
				foreach($uris as $j=> $list){
					$result = Str::startsWith($j, 'vk_');
					if($result){
						Route::get($list['grid'].'/pdf', [\App\Http\Controllers\PdfController::class, 'getTableAsPdf'])->name('pdf_print_'.$j);
					}
					/** routes grid**/
					Route::get($list['grid'], [\App\Http\Controllers\AdminGridController::class, 'index'])->name('grid_'.$j);
					/** routes grid get**/
					Route::get($list['grid'].'/get',  [\App\Http\Controllers\AdminGridController::class, 'getdata'])->name('grid_get_'.$j);
					/** routes create**/
					Route::get($list['create'], [\App\Http\Controllers\AdminFormController::class, 'index'])->name('create_'.$j);
					/** routes create get**/
					Route::get($list['create'].'/get', [\App\Http\Controllers\AdminFormController::class, 'getcreate'])->name('create_get_'.$j);
					/** routes edit**/
					Route::get($list['update'], [\App\Http\Controllers\AdminFormController::class, 'index'])->name('update_'.$j);
					/** routes edit get**/	
					Route::get($list['update'].'/get', [\App\Http\Controllers\AdminFormController::class, 'getupdate'])->name('update_get_'.$j);
					/** routes delete**/
					Route::delete($list['delete'], [\App\Http\Controllers\ProcessFormAdminController::class, 'destroy'])->name('delete_'.$j);
				}
			}
		}
	}
 
	/**
	 * Define las rutas de administración para la API.
	 * 
	 * @return void
	 */
	private static function _routesApi(){
			$uris=[];
			// Obtiene las rutas de administración o las tablas de información
			// Si no se activan las rutas de administración, se obtienen las tablas de información
			// Si se activan, se obtienen las rutas definidas en la configuración
			$data= self::$routeMenu;
			if(!empty($data)){
				foreach($data as $k=> $rows){
					$uris[$k]['create']= '/form/'.$k.'/create';		
					$uris[$k]['update']= '/form/'.$k.'/update/{id}';
					//$uris[$k]['delete']= $k.'/delete/{id}';
					foreach($uris as $j=> $list){
						/** routes create**/	  	
						\Route::post($list['create'], [\App\Http\Controllers\ProcessFormAdminController::class, 'store'])->name('create.insert_'.$j);
						/** routes update**/
						\Route::post($list['update'],  [\App\Http\Controllers\ProcessFormAdminController::class, 'update'])->name('update.insert_'.$j);
					}
				}
			}
		}

	
	private static function _setRoutes(){
		// Puedes descomentar la siguiente línea para activar las rutas de administración
		// Si no se activa, se obtienen las tablas de información	
		//\Config::set('appweb.admin.active', true);
		$data= [];
		if(\Config::get('appweb.admin.active')){
			$data= \Config::get('appweb.admin.routes');
		}
		else{
			// Si las rutas de administración no están activas, obtén las tablas de información		
			$data= self::getInformationTables();
			Log::debug('getInformationTables: ' . json_encode($data));
			if(empty($data)){
				Log::error('No se encontraron tablas de información en ' . __METHOD__);
				return [];
			}
		}

		self::$routeMenu = $data;
	}


}// fin de la clase AdminRoutes