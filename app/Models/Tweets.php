<?php
	
	namespace App\Models;
	use App\Models\Model;
	use App\Models\Tweet;
	use Exception;

	class Tweets extends Model{
		
		public static function getByHashtag($hashtag, $page){
			
			$min = 10 * $page;
			$max = 10 * ($page + 1);
			
			$Redis = Tweets::getRedis();
			$tweets = array();
			
			$uuids = $Redis -> zrevrange('hashtag:'.$hashtag, $min, $max);
			
			foreach($uuids as $uuid)
			
				$tweets[] = Tweet::getByUUID($uuid);
				
			return $tweets;
			
		}
		
	}
	
?>