<?php 

class Model {
	private $Host;
	private $Cache;

	public function __construct() {
		global $config;
		
		$host_class = str_replace(" ", "_", ucwords($config["host"]));		
		$this->Host = new $host_class();
		$this->Cache = new Cache();
		
		$this->get_all("pages");
	}
	
	public function get_all($path) {
		$path = trim_slashes($path);
		$path_segs = explode("/", trim_slashes($path));
		$type = $path_segs[0];
		
		$collection_files = ($type == "collections" and count($path_segs) > 1);
		
		if($type == "snippets" || $collection_files)
			$dirs_only = false;
		else
			$dirs_only = true;
	
		$names = $this->Host->dir_contents($path, $dirs_only);
		
		$items = array();
		
		foreach($names as $name)
			$items[] = $this->get_single("$path/$name");
			
		$this->Cache->update($type, $items);
		
		return $items;
	}
	
	public function get_single($path) {
		$type = array_shift(explode("/", trim_slashes($path)));
		$name = basename($path);
		
		switch($type) {
			case "posts":
			case "drafts":
			case "pages":
				$name_key = "slug";
				break;
			case "collections":
			case "snippets":
				$name_key = "title";
				break;	
			default:
				//if type not set or invalid, fail
				return false;
		}
		
		$cache_route = array_search_recursive($name, $this->Cache->$type, $name_key, false);
		
		if(!$cache_route) {		
			$item = $this->Host->get_single($path);
			
			if($item)
				$this->Cache->add($type, $item);

			return $item;
		}
		
		array_pop($cache_route);
		
		//construct array call from searched nodes
		eval('$item = $this->Cache->'.$type.'["'.implode('"]["', $cache_route).'"];');
		
		return $item;
	}
}