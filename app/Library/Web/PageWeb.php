<?php
namespace App\Library\Web;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Carbon\Carbon;


class CBD {

protected $parent_menu=[];
protected $submenu=[];
protected $dataSkeletonWeb=[];
public $routes=[];
protected $whereActiveId=[];
protected $dataTables=[];


	public function __Construct(){

		$this->get_menu();

		$this->GetDataCBD();
	}

	private function get_menu() {
		$titles = $this->get_titles();
		//$bodies = $this->get_bodies();
		//$headers = $this->get_headers();
		//$footers = $this->get_footers();
		//$categories = $this->get_categories();
		//$subcategories = $this->get_subcategories();
		$menu = $this->combine_menus($titles);
		return $menu;
	}

	private function get_titles() {
		try{
		return DB::table('vk_titles')
			->leftJoin(DB::raw('(SELECT id AS id_b, id_body as idbx, b_title  FROM vk_bodys ORDER BY id asc) as vk_bod'), 'vk_bod.idbx', '=', 'vk_titles.id')
			//->leftJoin(DB::raw('(SELECT id as id_h, id_header, h_title FROM vk_headers ORDER BY id_h) as vk_hed' ), 'vk_hed.id_header', '=', 'vk_titles.id' ) 
			//->leftJoin(DB::raw('(SELECT id as id_f, id_footer, f_title FROM vk_footers ORDER BY id_f) as vk_foo' ), 'vk_foo.id_footer', '=', 'vk_titles.id' ) 
			->leftJoin(DB::raw('(SELECT id as id_cat, categoryName FROM vk_categories ORDER BY id_cat) as vk_cat' ), 'vk_cat.id_cat', '=', 'vk_titles.id_name')
			->leftJoin(DB::raw('(SELECT id as id_subcat, id_category, subcatName FROM vk_subcategories ORDER BY id_subcat) as vk_subcat' ), 'vk_subcat.id_subcat', '=', 'vk_titles.id_sublink')
			->where(['vk_titles.status'=> '1'])
			->orderBy('vk_titles.created_at')
			->get()
			->toArray();
		} catch (\PDOException $e) {
			$message = $e->getMessage();
    		$sqlState = $e->getCode();dd($message);
			if($sqlState){
				
				echo "I disabled AdminRouteServiceProvider::class and WebServiceProvider::class \n in the config/app file. Any command executed through php artisan in the terminal will throw an error.";
			}
			else{
				$message = $e->getMessage();
			}
		}
	}


	private function combine_menus($titles){


			$data=[];
			$c= collect($titles);
			//dd($c);
			for($i=0; $i < $c->count(); $i++){
				$ic= $c[$i];
				$icid= $ic->id;
				
				$ctitle= Str::of($ic->title);
				$ctitle= $ctitle->slug('-');
				$ctitle= $ctitle->lower();
				$c_link= $ctitle->prepend('/');
				$c_title= $ctitle->value;

				$bname= Str::of($ic->b_title);
				$bname= $bname->slug('-');
				$bname= $bname->lower();
				$bsub_link= $bname->prepend('/');
				$bsublink_n= $bname->value;

				//$catName="";
				$catName= Str::of($ic->categoryName);
				$catName= $catName->slug('-');
				$catName= $catName->lower();
				$catName_link= $catName->prepend('/');
				$catName_n= $catName->value;
				//$subcat="";
				$subcat= Str::of($ic->subcatName);
				$subcat= $subcat->slug('-');
				$subcat= $subcat->lower();
				$subcat_link= $subcat->prepend('/');
				$subcat_n= $subcat->value; 

				/*status active*/
				$this->whereActiveId['wherein'][$icid]=$icid;
				$this->whereActiveId['wherein.title'][$icid]= $c_title;

				if($ic->position == 0){
	
				$this->parent_menu[$icid]["link"]= $c_link;
				$this->parent_menu[$icid]["label"]= $ic->title;
				//$this->parent_menu[$icid]["name-route"]= $catName;
				$this->parent_menu[$icid]["name"]= $catName_n.'.'.$c_title;		
				$this->parent_menu[$icid]["position"]= $ic->position;
				$this->parent_menu[$icid]["parent"]= $ic->id_parent;
				}
				elseif($ic->position == 1) {	 

					$this->submenu[$icid]["parent"]= $ic->id_parent;
					$this->submenu[$icid]["link"]= $catName_link.$subcat_link.$c_link;
					$this->submenu[$icid]["label"]= $ic->title;
					//$this->submenu[$icid]["name-route"]= $subcat;
					$this->submenu[$icid]["name"]= $catName_n.'.'.$subcat_n.'.'.$bsublink_n;
					$this->submenu[$icid]["position"]= $ic->position;

					if (empty($this->parent_menu[$ic->id_parent]["count"])) {
						$this->parent_menu[$ic->id_parent]["count"]=0;
					}
						$this->parent_menu[$ic->id_parent]["count"] ++;

				}
			}

	}

	private function dataCBD(Array $data){
		
		try{

			if(array_key_exists('table', $data) && array_key_exists('id', $data)){
				if(!empty($data['table']) && !empty($data['id'])){
					$query= "SELECT * FROM ".$data['table']." WHERE ".$data['id']." IN (". implode(', ',$data['wherein']).") ";
					$data= DB::select(DB::raw($query));
					return $data;
				}
			}
		}
		catch(\Exception $e){
			dd("<p>Config dataCBD Array[]</p>");
		}		
	}

	private function GetDataCBD(){
		
		$this->dataTables=[
				'vk_bodys'=>'id_body',
				//'vk_headers'=> 'id_header',
				//'vk_footers'=> 'id_footer'
				];
		if(array_key_exists('wherein', $this->whereActiveId)){
			
			$whereIn= $this->whereActiveId['wherein'];
			foreach($this->dataTables as $t => $c){
		
				$data= ['table'=> $t, 'id'=>$c, 'wherein'=> $whereIn];
				$this->dataSkeletonWeb[$t]= $this->dataCBD($data);
			}
		}
	}

}//endclass



class PageWeb extends CBD {

	public $path = "/";
	public $limit_index= 90;
	public $imagebase = "image.jpg";

	//load routes web!!
	public $WebRoutes=[];

	//load menu
	public $Menu=[];
	public $data=[];



	public function __Construct(){
		parent::__construct();
		/**ini routes web */
		$this->_routes();
		/**get data skeleton */
		$data= $this->dataSkeletonWeb;
		/**init menu***/
		$this->wfmenu();
		
		/***load data**/
		foreach($data as $k=> $rows){
			$c= count($rows);
			for($i=0; $i < $c; $i++){
				$row= $rows[$i];
				$this->data[$row->id]= $rows[$i];
			}
		}
		///dd($this);
		
		
	}


public function wfmenu() {

	$this->Menu= ['parent'=> $this->parent_menu, 'submenu'=>$this->submenu];	
	return $this;
}


protected function _routes(){

	$r= [];
	$parent_menu= $this->parent_menu;
	$submenu= $this->submenu;

	//dd($parent_menu, $submenu);
	foreach ($parent_menu as $k => $pval) {
		if (!empty($pval['count'])) {
			$r[$k]['link'] = $pval['link'];
			$r[$k]['name']= $pval['name'];
			foreach ($submenu as $j => $sval) {
				$r[$j]['link']= $sval['link'];
				$r[$j]['name']= $sval['name'];
			}
		}	
		else{
			$r[$k]['link']= $pval['link'];
			$r[$k]['name']= $pval['name'];
		}
	}
	//dd($r);
	$this->WebRoutes= $r;
}




}//endclass
