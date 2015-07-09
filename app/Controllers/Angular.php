<?php

	namespace App\Controllers;
	use App\Core\File;
	
	class Angular extends Controller{
		
		protected function index(){
			
			try{
				
				$File = new File($this -> app -> getViewsDir().'/html/layout.html');
				
				echo $File -> getContent();
				
			}
			
			catch(\Exception $e){
				
				$this -> app -> httpError(500, 'Internal Server Error', 'Error with the layout template.<br/>'.$e -> getMessage());
				
			}
			
		}
		
	}
	
?>