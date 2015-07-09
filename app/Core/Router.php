<?php
	
	namespace App\Core;
	
	class Router extends \AltoRouter {
		
		public function __construct($routesDir){
	
			/* Loading all the Routes */
			
			foreach(\App\Core\FileSystem::scandir($routesDir, true) as $file)
				
				include $file;
		
		}
		
		public function getBasePath(){
			
			return str_replace($_SERVER['DOCUMENT_ROOT'], '', '/'.dirname(dirname($_SERVER['SCRIPT_FILENAME'])));
			
		}
		
		public function redirect($name, $args = array()){
			
			header('Location: '.$this -> generate($name, $args), true, 303);
			die();
			
		}
		
		public function map($rest, $route, $callback, $name = null, $params = array()){
			
			if(is_string($callback)){
			
				list($controller, $method) = explode('@', $callback);
				$callback = array('\App\Controllers\\'.$controller, $method);
			
			}
			
			parent::map($rest, $route, $callback, $name);
			
		}
		
		
		
	}
	
?>