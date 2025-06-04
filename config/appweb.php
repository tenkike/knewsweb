<?php

return [

	

	/**timezone */

	'timezone'=> 'Europe/Madrid',

	'menu'=>[
		'home'=> 'home',
	],


	/**admin */
	'admin' => [
		
		'active'=> false,
		'routes'=>	[
				"failed_jobs" => "failed_jobs",
				"migrations" => "migrations",
				"password_resets" => "password_resets",
				"personal_access_tokens" => "personal_access_tokens",
				"users" => "users",
				"vk_bodys" => "vk_bodys",
				"vk_categories" => "vk_categories",
				"vk_portafolios" => "vk_portafolios",
				"vk_subcategories" => "vk_subcategories",
				"vk_titles" => "vk_titles"
	  	],

	  'icons'=>	[
			"failed_jobs" => "bug",
			"migrations" => "gears",
			"password_resets" => "code-compare",
			"personal_access_tokens" => "shield-halved",
			"users" => "users",
			"vk_bodys" => 'book',
			"vk_categories" => "network-wired",
			"vk_portafolios" => "briefcase",
			"vk_subcategories" => "sitemap",
			"vk_titles" => "layer-group"
  		],

		/**grid */
		'grid'=> [
			'title'=> 'list view',
			'cols'=> [

			],

			'actions'=>[
				//'foto'=> 'Url/fotografy',
				//'act2'=> 'url-act-2',
				//'act3'=> 'url-act-3'
			]
		],

		/**form */
		'form'=> [
			'title'=> 'form view',
			'rows'=> [
				
				]
		],
	]
	//end admin



];