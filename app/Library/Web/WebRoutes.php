<?php
namespace App\Library\Web;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Library\Web\PageWeb;

class WebRoutes {


	public $routes;

	function __Construct(PageWeb $pageweb){
		$this->routes=  $pageweb->WebRoutes;
		//$this->Menu=  $pageweb->Menu; 
		//dd($pageweb);
		
	}

	

	public function Routes(){
		
		\Route::middleware('web')->get('api/menu', function(PageWeb $data){
			  return response()->json($data->Menu);
		});

		\Route::middleware(['web'])->prefix('/')->group(function (){
			
			$this->RouteGroup();
		});
	}


	private function RouteGroup(){

		$routesWeb= $this->routes;

		$firstElementMenu= array_key_first($routesWeb);
			
		foreach($routesWeb as $i=> $routeWeb){
		
		if($firstElementMenu == $i){
				//dd($arrayf, $i, routeWeb);

				//$name= Str::of($r[$i]['name'])->camel()->value;
				//dd($name, $routeWeb['link']);
				\Route::permanentRedirect('/', $routeWeb['link']);
				\Route::get($routeWeb['link'], function(PageWeb $pages){
					$data['pages']= $pages->data;
					
					$json= response()->json($data);
					return View::make('layouts.appweb');
				
				})->name($routeWeb['name']);
			}else{
				
				//$name= Str::of($routeWeb['name'])->camel()->value;
				\Route::get($routeWeb['link'], function(PageWeb $pages){
					$data['pages']= $pages->data;
					$json= response()->json($data);
					return View::make('layouts.appweb', compact('json'));
				})->name($routeWeb['name']);
		}
		
		
		}

	}

}
