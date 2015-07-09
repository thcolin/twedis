<?php

	namespace App\Controllers\API;
	use App\Controllers\Controller;
	use App\Models\User;
	use Exception;
	
	class Auth extends Controller{
		
		public function __construct(){
			
			/* Middlewares */
			
			$this -> middlewares = array();
			
			/* Controller construct */
			
			parent::__construct();
			
		}
		
		protected function login(){
			
			try{
				
				$User = User::getByLogin($_REQUEST['username'], $_REQUEST['password']);
				
				$return = array(
					'token' => $User -> getToken()
				);
				
			}
			
			catch(Exception $e){
				
				$return = array(
					'error' => true,
					'httpCode' => 404,
					'message' => $e -> getMessage()
				);
				
			}
				
			$this -> app -> returnJSON($return);
			
		}
		
		protected function register(){
			
			try{
				
				if(!isset($_REQUEST['username']) || !isset($_REQUEST['password']) || !isset($_REQUEST['confirm']))
				
					throw new Exception('Veuillez compléter tout les champs.');
					
				User::register($_REQUEST['username'], $_REQUEST['password'], $_REQUEST['confirm']);
				
				$return = array(
					'message' => 'Vous êtes maintenant inscris !'	
				);
				
			}
			
			catch(Exception $e){
				
				$return = array(
					'error' => true,
					'message' => $e -> getMessage()
				);
				
			}
			
			$this -> app -> returnJSON($return);
			
		}
		
	}
	
?>