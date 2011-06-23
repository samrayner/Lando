<?php 

class Model {
	private $Host;
	private $Cache;

	public function __construct() {
		global $config;
		
		$host_class = str_replace(" ", "_", ucwords($config["host"]));		
		$this->Host = new $host_class();
		$this->Cache = new Cache();
	}
	
	private function content_sort($a, $b) {
		if(!is_object($a))
			return 0;
		
		$result = 0;
	
		//order > not ordered
		if(isset($a->order) && !isset($b->order))
			$result = -1;
		if(!isset($a->order) && isset($b->order))
			$result = 1;
			
		//low order > high order
		if(isset($a->order) && isset($b->order))
			$result = $a->order - $b->order;
		
		//if custom order
		if($result != 0)
			return $result;
		
		//if not custom order
		switch(get_class($a)) {
			case "Page":
			case "Snippet":
			case "Collection":
				return strnatcmp($a->title, $b->title); //alphabetical
			case "Post":
				return $b->published - $a->published; //descending timestamp (newest first)
			default:
				return $b->modified - $a->modified; //descending timestamp (newest first)
		}
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
		
		foreach($names as $name) {
			$item = $this->get_single("$path/$name", false);
			$items[] = $item;
		}
		
		usort($items, array($this, "content_sort"));
			
		$this->Cache->update($type, $items);
		
		return $items;
	}
	
	public function get_single($path, $cache=true) {
		$path = trim_slashes($path);
		$type = array_shift(explode("/", $path));
		
		if($type == "pages") {
			$fixed_path = $path;
			$numbered_path = str_replace("/", "/(\d+\.\s*)?", $path); //match numbered pages in cache
			$path = $numbered_path;
		}
		
		$cache_route = array_search_recursive('~'.$path.'$~i', $this->Cache->$type, "path", true);
		
		if(!$cache_route) {	
			if($type == "pages")
				$path = $fixed_path; //return to fixed path for host fetch
		
			$item = $this->Host->get_single($path);
			
			if($item && $cache) {
				$this->Cache->add($type, $item);
				$this->Cache->save($type);
			}

			return $item;
		}
		
		array_pop($cache_route);
		
		$item = $this->Cache->$type;
		
		//step through cache to get search result node
		foreach($cache_route as $next_key) {
			//if a content object, convert to an array
			if(is_object($item))
				$item = $item->$next_key;
			elseif(is_array($item))
				$item = $item[$next_key];
		}
		
		return $item;
	}
}