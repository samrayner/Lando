<?php

class Cache {
	const CACHE_EXT = ".cache";
	private static $cache_dir;
		
	public function __construct() {
		self::$cache_dir = $_SERVER["DOCUMENT_ROOT"]."/app/cache/";
	}

	public function update($path, $content) {
		$path = self::$cache_dir.$path.self::CACHE_EXT;
		return file_put_contents($path, htmlspecialchars(serialize($content), ENT_QUOTES));
	}
	
	public function delete($path) {
		$path = self::$cache_dir.$path.self::CACHE_EXT;
		return unlink($path);
	}
	
	public function age($path) {
		$path = self::$cache_dir.$path;
		
		if(!is_dir($path))
			$path .= self::CACHE_EXT;
	
		//if file doesn't exist return current time as age
		return time()-(int)@filemtime($path);
	}
	
	public function dir_contents($dir) {
		$path = self::$cache_dir.$dir;
		
		if(!is_dir($path))
			return array();
	
		$paths = glob($path."/*".self::CACHE_EXT);
		$names = array();
		
		foreach($paths as $i => $file) {
			if(!is_dir($file))
				$names[] = basename($file);
		}
		
		return $names;
	}
	
	public function get_single($path) {
		$path = self::$cache_dir.$path.self::CACHE_EXT;
		$contents = @file_get_contents($path);
		
		if(!$contents)
			return false;
		
		return unserialize(htmlspecialchars_decode($contents, ENT_QUOTES));
	}
}