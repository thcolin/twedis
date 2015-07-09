<?php
	
	namespace App\Core;
	use \Exception;
	use App\Core\Parser;

	class File extends \SplFileInfo implements \JsonSerializable{
		
		public function __construct($path, $securedPaths = array(), $showHidden = false){
			
			# Main construct
			
			parent::__construct($path);
			
			# Hidden files
			
			$this -> showHidden = $showHidden;
			
			# Security
			
			if(!file_exists(realpath($path)))
			
				throw new Exception('Ce fichier n\'existe pas ('.$path.')');
			
			$this -> securedPaths = $securedPaths;
			
			foreach($this -> securedPaths as $key => $securedPath)
			
				$this -> securedPaths[$key] = realpath($securedPath);
			
			if($this -> securedPaths)
			
				self::isSecure();
				
			# Icon
				
			if($this -> isDir())
			
				$this -> icon = 'fa-folder';
				
			else if($this -> isVideo())
			
				$this -> icon = 'fa-file-video-o';
				
			else if($this -> isAudio())
			
				$this -> icon = 'fa-file-audio-o';
				
			else if($this -> isArchive())
			
				$this -> icon = 'fa-file-archive-o';
				
			else if($this -> isImage())
			
				$this -> icon = 'fa-file-image-o';
				
			else if($this -> isText())
			
				$this -> icon = 'fa-file-text-o';
				
			else
			
				$this -> icon = 'fa-file-o';
			
		}
		
		public function __toString(){
			
			return $this -> getRealPath();
			
		}
		
		public function delete(){
			
			if(!in_array($this -> getRealPath(), $this -> securedPaths))
				
				FileSystem::rm($this -> getRealPath());
			
			else
			
				throw new Exception('Vous ne pouvez pas supprimer le dossier racine.');
			
		}
		
		public function rename($newName){
			
			rename($this -> getRealPath(), $this -> getPath().'/'.basename($newName));
			
		}
		
		public function getContent(){
			
			return file_get_contents($this -> getRealPath());
			
		}
		
		public function getSize(){
			
			return FileSystem::size($this -> getRealPath());
			
		}
		
		public function getIcon(){
			
			return $this -> icon;
			
		}
		
		public function getPathBasename(){
			
			return basename($this -> getPath());
			
		}
		
		public function getFilename(){
			
			return $this -> getBasename(($this -> isFile() ? '.'.$this -> getExtension():null));
			
		}
		
		public function getParsedPerms(){
			
			return Framework::parsePerms($this -> getPerms());
			
		}
		
		public function getRelativePath(){
			
			foreach($this -> securedPaths as $securedPath){
					
				if($this -> isInPath($securedPath))
				
					return substr($this -> getRealPath(), strlen($securedPath) + 1);
				
			}
				
			return basename($this -> getRealPath());
			
		}
		
		public function getParsedATime(){
			
			return Parser::parseTime($this -> getATime(), '%d/%m/%Y - %H:%M:%S');
			
		}
		
		public function getParsedCTime(){
			
			return Parser::parseTime($this -> getCTime(), '%d/%m/%Y - %H:%M:%S');
			
		}
		
		public function getParsedMTime(){
			
			return Parser::parseTime($this -> getMTime(), '%d/%m/%Y - %H:%M:%S');
			
		}
		
		public function getParsedSize(){
			
			return Parser::parseSize(FileSystem::size($this -> getRealPath()));
			
		}
		
		public function getOwnerArray(){
			
			return posix_getpwuid($this -> getOwner());
			
		}
		
		public function getGroupArray(){
			
			return posix_getgrgid($this -> getGroup());
			
		}
		
		public function isHidden(){
			
			$visible = 1;
			
			foreach(explode('/', $this -> getRealPath()) as $value)
			
				$visible *= (($this -> showHidden OR substr($value, 0, 1) != '.') ? 1:0);
				
			return !$visible;
			
		}
		
		public function isInPath($path){
			
			return (substr($this -> getRealPath(), 0, strlen($path)) == $path);
			
		}
		
		public function isVideo(){
			
			return in_array($this -> getExtension(), array('avi', 'mkv', 'wmv', 'mp4'));
			
		}
		
		public function isAudio(){
			
			return in_array($this -> getExtension(), array('mp3', 'flac'));
			
		}
		
		public function isArchive(){
			
			return in_array($this -> getExtension(), array('rar', '7z', 'zip'));
			
		}
		
		public function isImage(){
			
			return in_array($this -> getExtension(), array('jpeg', 'jpg', 'bmp', 'png'));
			
		}
		
		public function isText(){
			
			return in_array($this -> getExtension(), array('txt', 'nfo'));
			
		}
		
		public function isSample(){
			
			return ($this -> isVideo() && (preg_match('#sample#', $this -> getFilename()) OR $this -> getSize() < 20000000));
			
		}
		
		public function isx264(){
			
			if(!$this -> isVideo())
			
				return false;
				
			if(!$this -> mediaInfo)
			
				$this -> getMediaInfo();
				
			$mediaInfoArray = array();
			
			foreach(explode("\n", $this -> mediaInfo) as $line){
				
				$explode = explode(':', $line);
				
				if(isset($explode[1]))
				
					$mediaInfoArray[trim($explode[0])] = trim($explode[1]);
				
			}
			
			if(substr($mediaInfoArray['Writing library'], 0, 4) == 'x264')
		
				return true;
			
		}
		
		public function getMediaInfo(){
			
			$this -> mediaInfo = shell_exec('/usr/bin/mediainfo "--output=...y" '.escapeshellarg($this -> getRealPath()));
			
			return $this -> mediaInfo;
			
		}
		
		public function isSecuredPath(){
				
			foreach($this -> securedPaths as $securedPath){
			
				if($this -> getRealPath() == $securedPath)
				
					return true;
				
			}
			
			return false;
			
		}
		
		public function isSecure(){
			
			if(!file_exists($this -> getRealPath()))
			
				throw new Exception('Ce fichier/dossier n\'existe pas.');
			
			if(!$this -> isReadable())
			
				throw new Exception('Ce fichier/dossier n\'est pas lisible.');
			
			if(!$this -> isWritable())
			
				throw new Exception('Le serveur web n\'a pas les droits d\'écriture sur ce fichier/dossier.');
				
			if($this -> isHidden())
			
				throw new Exception('Ce fichier/dossier est caché.');
			
			if(!$this -> securedPaths)
			
				return true;
			
			else{
				
				foreach($this -> securedPaths as $securedPath){
				
					if($this -> getRealPath() == $securedPath)
					
						return true;
						
					else if($this -> isInPath($securedPath))
					
						return true;
					
				}
				
				throw new Exception('Vous n\'avez pas le droit d\'accéder à ce fichier/dossier.');
				
			}
			
		}
		
		public function jsonSerialize(){
			
			$array = array();
			
			foreach(get_class_methods(__CLASS__) as $method){
				
				if(substr($method, 0, 3) == 'get'){
					
					if(in_array($method, array('getFilename', 'getRelativePath', 'getIcon', 'getBasename', 'getParsedCTime', 'getParsedATime', 'getParsedMTime', 'getParsedSize')))
				
						$array[lcfirst(substr($method, 3))] = $this -> $method();
					
				}
				
				else if(substr($method, 0, 2) == 'is'){
					
					if(in_array($method, array('isFile', 'isVideo', 'isImage', 'isAudio', 'isArchive', 'isText')))
				
						$array[$method] = $this -> $method();
				
				}
				
			}
			
			return $array;
			
		}
		
	}
	
?>