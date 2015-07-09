<?php
	
	namespace App\Middlewares\Auth;
	use App\Models\User;
	use Exception;

	class Logged extends \App\Middlewares\Middleware {
		
		public function handle(){
			
			try{
				
				if(!isset($_SERVER['HTTP_X_TOKEN']))
				
					throw new Exception('Aucun token.');
					
				$token = $_SERVER['HTTP_X_TOKEN'];
					
				$User = User::getByToken($token);
				
				$this -> app -> User = $User;
				
			}
			
			catch(Exception $e){
				
				$this -> app -> httpError(401, 'Unauthorized');
				
			}
			
		}
		
	}
	
?>