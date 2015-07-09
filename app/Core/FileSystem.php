<?php
	
	namespace App\Core;

	class FileSystem{
		
		/* Homemade "scandir" function : recursive, delete useless values (., .., .DS_Store..) */

		public static function scandir($path, $recursive = false, $directory = false){
			
			$return = array();
			
			$path = realpath($path);
			
			foreach(scandir($path) as $tmp){
				
				if(!in_array($tmp, array('.', '..', '.DS_Store', '.svn'))){
					
					$tmp = $path.'/'.$tmp;
					
					if(is_dir($tmp)){
						
						if($directory)
						
							$return[] = $tmp;
						
						if($recursive)
						
							$return = array_merge($return, self::scandir($tmp, $recursive, $directory));
						
					}
					
					else if(is_file($tmp))
					
						$return[] = $tmp;
					
				}
				
			}
			
			natcasesort($return);

			return $return;

		}
		
		/* Return the size in octets */
		
		public static function size($path){
		
			if(is_file($path)) return(filesize($path));
			
			else if(is_dir($path)){
				
				$return = 0;
				
				foreach(glob($path.'/*') as $fn)
				
					$return += self::size($fn);
					
				return($return);
				
			}
			
			else return(0);
		
		}
		
	}
	
?>