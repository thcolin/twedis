<?php
	
	namespace App\Core;
	use \Illuminate\Database\Capsule\Manager as Capsule;
	use \Twig_Loader_Filesystem;
	use \Twig_Environment;

	class App{
		
		private static $_instance;
		
		/* Return the instance of the object */
		
		public static function getInstance($config = null){
			
			if(is_null(self::$_instance))
			
				self::$_instance = new App($config);
				
			return self::$_instance;
			
		}
		
		/* Construct */
		
		protected function __construct($config = array()){
			
			/* Session */
			
			session_start();
			
			/* Config array */
			
			$this -> config = $config;
			
			/* Router */
			
			$this -> router = new Router(self::getRoutesDir());
			$this -> router -> addMatchTypes(array('z', '[^/]++'));
			$this -> router -> setBasePath($this -> router -> getBasePath());
			
			/* Twig */
			
			$loader = new Twig_Loader_Filesystem(self::getViewsDir());
			$this -> twig = new Twig_Environment($loader);
			
			$this -> twig -> addGlobal('app', $this);
			
			/* SQL */
			
			if(is_file(self::getConfigDir().'/database.php')){
				
				$database = include self::getConfigDir().'/database.php';
				
				$this -> capsule = new Capsule;
				$this -> capsule -> addConnection($database);
				$this -> capsule -> bootEloquent();
				
			}
				
			/* Assetic */
			
			$config = include self::getConfigDir().'/bower.php';
			
			$this -> assets = new Assets($config);
			
			if($this -> config['dev'])
				
				$this -> assets -> build(self::getPublicDir().'/assets');
			
		}
		
		/* Paths */
		
		public static function getProjectDir(){
			
			return dirname(dirname(__DIR__));
			
		}
		
		public static function getPublicDir(){
			
			return self::getProjectDir().'/public';
			
		}
		
		public static function getAppDir(){
			
			return self::getProjectDir().'/app';
			
		}
		
		public static function getConfigDir(){
			
			return self::getAppDir().'/config';
			
		}
		
		public static function getControllersDir(){
			
			return self::getAppDir().'/Controllers';
			
		}
		
		public static function getRoutesDir(){
			
			return self::getAppDir().'/routes';
			
		}
		
		public static function getStorageDir(){
			
			return self::getAppDir().'/storage';
			
		}
		
		public static function getViewsDir(){
			
			return self::getAppDir().'/views';
			
		}
		
		/* HTTP */
		
		public function httpError($code, $title, $body = ''){
			
			header($_SERVER['SERVER_PROTOCOL'].' '.intval($code).' '.$title, true, $code);
			echo $this -> twig -> render('html/framework/httpError.twig', array('code' => $code, 'title' => $title, 'body' => $body));
			die();
			
		}
		
		public function returnJSON($array){
			
			if(isset($array['error']))
				
				http_response_code(isset($array['httpCode']) ? intval($array['httpCode']):400);
				
			else
			
				http_response_code(200);
				
			unset($array['httpCode']);
			
			header('Content-Type: application/json');
			
			echo(json_encode($array, JSON_UNESCAPED_UNICODE));
			die();
			
		}
		
		/* Application Errors */
		
		public function addError($error){
			
			if(!isset($_SESSION['errors']))
			
				$_SESSION['errors'] = array();
			
			$_SESSION['errors'][] = $error;
			
		}
		
		public function getErrors(){
			
			$errors = (isset($_SESSION['errors']) ? $_SESSION['errors']:array());
			$_SESSION['errors'] = array();
			
			return $errors;
			
		}
		
	}
	
?>