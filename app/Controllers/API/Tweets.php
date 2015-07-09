<?php

	namespace App\Controllers\API;
	use App\Controllers\Controller;
	use App\Models\User;
	use App\Models\Tweets as TweetsModel;
	use Exception;
	
	class Tweets extends Controller{
		
		public function __construct(){
			
			/* Middlewares */
			
			$this -> middlewares = array(
				'Auth\Logged'
			);
			
			/* Controller construct */
			
			parent::__construct();
			
		}
		
		protected function getTimeline($page){
			
			try{
					
				$tweets = $this -> app -> User -> getTimeline($page);
				
				$return = array('tweets' => $tweets);
				
			}
			
			catch(Exception $e){
				
				$return = array(
					'error' => true,
					'message' => $e -> getMessage()
				);
				
			}
				
			$this -> app -> returnJSON($return);
				
		}
		
		protected function getByUsername($username, $page){
			
			try{
				
				$User = User::getByUsername($username);
				
				$return['tweets'] = $User -> getTweets($page);
			
			}
			
			catch(Exception $e){
				
				$return = array(
					'error' => true,
					'message' => $e -> getMessage()
				);
				
			}
				
			$this -> app -> returnJSON($return);
			
		}
		
		protected function getByHashtag($hashtag, $page){
			
			try{
					
				$tweets = TweetsModel::getByHashtag($hashtag, $page);
				
				$return = array(
					'tweets' => $tweets
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
		
		protected function tweet(){
			
			try{
				
				# Empty
				
				if(!isset($_REQUEST['tweet']))
				
					throw new Exception('Le tweet est vide.');
					
				$Tweet = $this -> app -> User -> tweet($_REQUEST['tweet']);
				
				$return = array(
					'message' => 'Votre tweet à bien été envoyé !',
					'tweet' => $Tweet
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