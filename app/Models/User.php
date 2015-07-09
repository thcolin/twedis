<?php
	
	namespace App\Models;
	use Rhumsaa\Uuid\Uuid;
	use App\Models\Model;
	use App\Models\Tweet;
	use Exception;
	use JWT;

	class User extends Model{
		
		const SECRET = '1IOzaB2znczHp083Gduzojiyufdez';
		
		public function __construct($vars){
			
			parent::__construct($vars);
			
		}
		
		public static function getByToken($token){
			
			try{
			
				$obj = JWT::decode($token, User::SECRET, array('HS256'));
				
				if(!$obj)
				
					throw new Exception('Le token n\'est pas valide.');
					
				$uuid = $obj -> uuid;
				
				return User::getByUuid($uuid);
			
			}
			
			catch(Exception $e){
				
				throw new Exception('Le token n\'est pas valide.');
				
			}
			
		}
		
		public static function getByUUID($uuid){
			
			$data = User::getDataByUUID($uuid);
			
			return new User($data);
			
		}
		
		public static function getByUsername($username){
			
			$uuid = User::getUUIDByUsername($username);
			
			$data = User::getDataByUUID($uuid);
			
			return new User($data);
			
		}
		
		public static function register($username, $password, $confirm){
				
			# Empty
			
			if(!$username || !$password || !$confirm)
			
				throw new Exception('Nom d\'utilisateur, mot de passe ou confirmation de celui-ci manquant.');
				
			# Only a-zA-Z
			
			if(preg_match('#\W+#', $username))
			
				throw new Exception('Le nom d\'utilisateur ne peux pas contenir de caractères spéciaux.');
				
			# Confirm password
				
			if($password != $confirm)
			
				throw new Exception('Les deux mots de passe ne sont pas identiques.');
				
			# Already taked
			
			if(User::checkUsername($username))
			
				throw new Exception('Ce nom d\'utilisateur à déjà été choisis.');
				
			# Everything is good
			
			$uuid = Uuid::uuid4();
			
			$data = array(
				'uuid' => $uuid,
				'username' => $username,
				'password' => User::crypt($password),
				'tweets' => 0
			);
			
			$Redis = User::getRedis();
			
			$Redis -> hmset('user:'.$uuid, $data);
			$Redis -> hset('users', $username, $uuid);
				
		}
		
		public static function getByLogin($username, $password){
			
			# Errors
				
			if(!$username || !$password)
			
				throw new Exception('Nom d\'utilisateur ou mot de passe manquant.');
				
			# Crypt
				
			$crypted = User::crypt($password);
			
			# Try
			
			$uuid = User::getUUIDByUsername($username);
			
			$data = User::getDataByUUID($uuid);
			
			if($data['password'] != $crypted)
			
				throw new Exception('Mot de passe incorrect.');
			
			# Logged, return instance of User
			
			return new User($data);
			
		}
		
		private static function crypt($password){
			
			return sha1($password.md5(User::SECRET));
			
		}
		
		public function getToken(){
			
			# if (array), we got the $this -> Redis Object causing bugs when decoding the token
			$array = json_decode(json_encode($this), true);
			
			return JWT::encode($array, User::SECRET);
			
		}
		
		public function getFollowers(){
			
			return $this -> Redis -> zrevrange('user:'.$this -> uuid.':followers', 0, -1);
			
		}
		
		public function getFollowing(){
			
			return $this -> Redis -> zrevrange('user:'.$this -> uuid.':following', 0, -1);
			
		}
		
		public function toggleFollow($User, $bool){
				
			if($bool){
			
				# Current User Following
				$this -> Redis -> zadd('user:'.$this -> uuid.':following', time(), $User -> username);
				
				# Followed User
				$this -> Redis -> zadd('user:'.$User -> uuid.':followers', time(), $this -> username);
				
			}
				
			else{
			
				# Current User Following
				$this -> Redis -> zrem('user:'.$this -> uuid.':following', $User -> username);
				
				# Followed User
				$this -> Redis -> zrem('user:'.$User -> uuid.':followers', $this -> username);
				
			}
			
		}
		
		public function getTimeline($page){
			
			$tweets = array();
			$hsets = array('user:'.$this -> uuid.':tweets');
			
			$min = 10 * $page;
			$max = 10 * ($page + 1);
			
			# Generate timeline
			
			$followings = $this -> getFollowing();
			
			foreach($followings as $following)
			
				$hsets[] = 'user:'.User::getUUIDByUsername($following).':tweets';
			
			$this -> Redis -> zunionstore('user:'.$this -> uuid.':timeline', $hsets);
			
			$timeline = $this -> Redis -> zrevrange('user:'.$this -> uuid.':timeline', $min, $max);
			
			foreach($timeline as $uuid)
			
				$tweets[] = Tweet::getByUUID($uuid);
			
			return $tweets;
			
		}
		
		public function getTweets($page){
			
			$min = 10 * $page;
			$max = 10 * ($page + 1);
				
			$tweets = array();
				
			$uuids = $this -> Redis -> zrevrange('user:'.$this -> uuid.':tweets', $min, $max);
			
			foreach($uuids as $uuid)
				
				$tweets[] = Tweet::getByUUID($uuid);
			
			return $tweets;
			
		}
		
		public function tweet($message){
				
			# Empty
			
			if(!$message)
			
				throw new Exception('Le tweet est vide.');
				
			# Too large
				
			if(strlen($message) > 140)
			
				throw new Exception('Un tweet ne peux pas dépasser 140 caractères.');
			
			# Tweet
			
			$Tweet = Tweet::tweet($this, $message);
			
			return $Tweet;
			
		}
		
		/* Private Redis Methods */
		
		private static function getUUIDByUsername($username){
			
			$Redis = User::getRedis();
			
			$uuid = $Redis -> hget('users', $username);
			
			if(!$uuid)
			
				throw new Exception('Cet utilisateur n\'existe pas.');
				
			return $uuid;
			
		}
		
		private static function getDataByUUID($uuid){
			
			$Redis = User::getRedis();
			
			$data = $Redis -> hgetall('user:'.$uuid);
			
			if(!$data)
			
				throw new Exception('Cet utilisateur n\'existe pas.');
				
			return $data;
			
		}
		
		private static function checkUsername($username){
			
			$Redis = User::getRedis();
			
			$uuid = $Redis -> hget('users', $username);
			
			return($uuid ? true:false);
			
		}
		
	}
	
?>