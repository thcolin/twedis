<?php

	namespace App\Controllers\API;
	use App\Controllers\Controller;
	use App\Models\User as UserModel;
	use Rhumsaa\Uuid\Uuid;
	use Exception;
	
	class User extends Controller{
		
		public function __construct(){
			
			/* Middlewares */
			
			$this -> middlewares = array(
				'Auth\Logged'
			);
			
			/* Controller construct */
			
			parent::__construct();
			
		}
		
		protected function getByUsername($username){
			
			try{
				
				$User = UserModel::getByUsername($username);
			
				$User -> following = $User -> getFollowing();
				$User -> followers = $User -> getFollowers();
				
				$this -> app -> returnJSON(['user' => $User]);
			
			}
			
			catch(Exception $e){
				
				$return = array(
					'error' => true,
					'message' => $e -> getMessage()
				);
				
			}
				
			$this -> app -> returnJSON($return);
			
		}
		
		protected function getCurrentUser(){
			
			$this -> app -> User -> following = $this -> app -> User -> getFollowing();
			$this -> app -> User -> followers = $this -> app -> User -> getFollowers();
				
			$this -> app -> returnJSON(['user' => $this -> app -> User]);
			
		}
		
		protected function toggleFollow($username, $bool){
			
			try{
				
				$Following = UserModel::getByUsername($username);
				
				$this -> app -> User -> toggleFollow($Following, $bool);
			
				$return = array(
					'message' => 'Traitement effectué'
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