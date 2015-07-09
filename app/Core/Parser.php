<?php
	
	namespace App\Core;

	class Parser{
		
		/* Parse Time in $format */
		
		public static function parseTime($time, $format = '%d/%m/%Y'){
			
			return strftime($format, $time);
			
		}
		
		/* Parse duration, with seconds or not */
		
		public static function parseDuration($seconds, $displaySeconds = false){
		
			$return = array();
		
			$now = new DateTime("@0");
			$time = new DateTime("@$seconds");
			$interval = $now -> diff($time);
			
			if(($d = $interval -> d) > 0) $return[] = $d.' jour'.($d > 1 ? 's':'');
			if(($h = $interval -> h) > 0) $return[] = $h.' heure'.($h > 1 ? 's':'');
			if(($i = $interval -> i) > 0) $return[] = $i.' minute'.($i > 1 ? 's':'');
			if(($s = $interval -> s) > 0 && $displaySeconds) $return[] = $s.' seconde'.($s > 1 ? 's':'');
			
			return implode(' ', $return);
		
		}
		
		/* Parse size from octets to human readable */
		
		public static function parseSize($size){
			
			$units = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po', 'Eo', 'Zo', 'Yo');
			$power = $size > 0 ? floor(log($size, 1024)) : 0;
			return number_format($size / pow(1024, $power), 2, '.', ',').' '.$units[$power];
			
		}
		
		/* Parse speed from octets to human readable */
		
		public static function parseSpeed($size){
		
			return self::parseSize($size).'/s';
			
		}
		
		/* Parse UNIX permissions from 0777 to human readable */
		
		public static function parsePerms($perms){
			
			if (($perms & 0xC000) == 0xC000)
			
				$info = 's';
			
			elseif (($perms & 0xA000) == 0xA000)
			
				$info = 'l';
			
			elseif (($perms & 0x8000) == 0x8000)
			
				$info = '-';
			
			elseif (($perms & 0x6000) == 0x6000)
			
				$info = 'b';
			
			elseif (($perms & 0x4000) == 0x4000)
			
				$info = 'd';
			
			elseif (($perms & 0x2000) == 0x2000)
			
				$info = 'c';
			
			elseif (($perms & 0x1000) == 0x1000)
			
				$info = 'p';
			
			else
			
				$info = 'u';
			
			// Owner
			$info .= (($perms & 0x0100) ? 'r' : '-');
			$info .= (($perms & 0x0080) ? 'w' : '-');
			$info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));
			
			// Group
			$info .= (($perms & 0x0020) ? 'r' : '-');
			$info .= (($perms & 0x0010) ? 'w' : '-');
			$info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));
			
			// World
			$info .= (($perms & 0x0004) ? 'r' : '-');
			$info .= (($perms & 0x0002) ? 'w' : '-');
			$info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));
	
			return $info;
			
		}
	
	}
	
?>