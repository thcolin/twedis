<?php
	
	namespace App\Middlewares;
	
	class Middleware{
		
		public function __construct(){
			
			$this -> app = \App\Core\App::getInstance();
			
		}
		
	}
	
?>