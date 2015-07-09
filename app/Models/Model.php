<?php
	
	namespace App\Models;
	
	abstract class Model{
		
		public $Redis;
		
		public function __construct($vars){
			
			foreach($vars as $key => $var)
			
				$this -> $key = $var;
				
			$this -> Redis = Model::getRedis();
			
		}
		
		public static function getRedis(){
			
			$app = \App\Core\App::getInstance();
			return $app -> Redis;
			
		}
		
	}
	
?>