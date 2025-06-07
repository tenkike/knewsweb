<?php
namespace App\Library\Crud;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\View;
use App\Library\Crud\DataSchema;

use Illuminate\Support\Facades\Route;

class AdminRoutes extends DataSchema {

	private static $getDataCols;
	
	public function __construct()
	{
			parent::__construct();
			$this->routeMenu= self::_setRoutes();	
	}

	public static function init_routes(){
	
		Route::middleware(['web', 'auth'])->prefix('admin')->group(function(){
			self::_routes();
		});

		Route::middleware(['api'])->prefix('api/admin')->group(function(){
			self::_routesApi();
		});
		
	}

   

	private static function _routes(){
		
		$data= self::_setRoutes();
		
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

				Route::get($list['grid'].'/get',  [\App\Http\Controllers\AdminGridController::class, 'getdata'])->name('grid_get_'.$j);

				/** routes create**/
				Route::get($list['create'], [\App\Http\Controllers\AdminFormController::class, 'index'])->name('create_'.$j);
				
				Route::get($list['create'].'/get', [\App\Http\Controllers\AdminFormController::class, 'getcreate'])->name('create_get_'.$j);
			  	
			  	/** routes edit**/

			  	Route::get($list['update'], [\App\Http\Controllers\AdminFormController::class, 'index'])->name('update_'.$j);

				Route::get($list['update'].'/get', [\App\Http\Controllers\AdminFormController::class, 'getupdate'])->name('update_get_'.$j);

			  	/** routes delete**/
				Route::delete($list['delete'], [\App\Http\Controllers\ProcessFormAdminController::class, 'destroy'])->name('delete_'.$j);

			}
			
		}

	}
	}
 
	private static function _routesApi(){
		
		$data= self::_setRoutes();
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

			//\Config::set('appweb.admin.active', true);
			
		$data=[];

		if(\Config::get('appweb.admin.active')){
			$data= \Config::get('appweb.admin.routes');
		}
		else{
			$data= self::getInformationTables();
		}

		//dd($data);

		return $data;
		
	}


}