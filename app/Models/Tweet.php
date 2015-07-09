<?php
	
	namespace App\Models;
	use Rhumsaa\Uuid\Uuid;
	use App\Models\Model;
	use App\Models\User;
	use Exception;

	class Tweet extends Model{
		
		public function __construct($vars){
			
			parent::__construct($vars);
			
		}
		
		public static function tweet($User, $message){
			
			$Redis = Tweet::getRedis();
				
			$uuid = Uuid::uuid4();
			$timestamp = time();
			
			# Tweet
			
			$tweet = array(
				'uuid' => $uuid,
				'author' => $User -> username,
				'time' => $timestamp,
				'message' => $message
			);
			
			$Redis -> hmset('tweet:'.$uuid, $tweet);
			
			#User
			
			$Redis -> zadd('user:'.$User -> uuid.':tweets', $timestamp.rand(0, 9), $uuid);
			$Redis -> hincrby('user:'.$User -> uuid, 'tweets', 1);
			
			# Hashtags
			
			preg_match_all('#(\#[a-zA-Z0-9]+)#', $message, $hashtags);
			
			foreach($hashtags[1] as $hashtag)
			
				$Redis -> zadd('hashtag:'.substr($hashtag, 1), $timestamp, $uuid);
				
			return $tweet;
			
		}
		
		public static function getByUUID($uuid){
			
			$Redis = Tweet::getRedis();
			
			$tweet = $Redis -> hgetall('tweet:'.$uuid);
			
			if(!$tweet)
			
				throw new Exception('Ce tweet n\'existe pas.');
				
			return $tweet;
			
		}
		
	}
	
?>