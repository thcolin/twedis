<?php

	namespace App\Controllers;
	use Exception;
	
	class Controller{
		
		protected $middlewares = array();
		
		public function __construct(){
			
			$this -> app = \App\Core\App::getInstance();
			
		}
		
		public function __call($method, $args){
			
			if(!method_exists($this, $method))
			
				throw new Exception('The method "'.$method.'" does not exist in this object.');
				
			/* Call the Middlewares */
				
			$middlewares = array();
			
			foreach($this -> middlewares as $key => $middleware){
			
				# Global Middleware
			
				if(is_int($key))
				
					$middlewares[] = $middleware;
				
				# Selected Method(s)
				
				else if(is_string($key)){
					
					# One Method
					
					if(is_string($middleware) && $middleware == $method)
					
						$middlewares[] = $key;
					
					# Multiple Methods
					
					else if(is_array($middleware)){
						
						foreach($middleware as $_method){
							
							if($_method == $method)
						
								$middlewares[] = $key;
							
						}
						
					}
					
				}
				
			}
				
			$middlewares = array_unique($middlewares);
			
			foreach($middlewares as $class){
				
				$class = '\App\Middlewares\\'.$class;
				$middleware = new $class();
			
				call_user_func(array($middleware, 'handle'));
				
			}
			
			/* Call the method */
			
			call_user_func_array(array($this, $method), $args);
			
		}
		
	}
	
?>