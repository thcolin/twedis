<?php

	namespace App\Controllers\API;
	use \App\Controllers\Controller as Controller;
	
	class Template extends Controller{
		
		public function get($template){
			
			try{
				
				$File = new \App\Core\File($this -> app -> getViewsDir().'/'.$template, array($this -> app -> getViewsDir()));
				echo $File -> getContent();
				
			}
			
			catch(\Exception $e){
				
				$this -> app -> httpError(404, 'Not Found', 'The requested URL was not found on this server.');
				
			}
			
		}
		
	}
	
?>