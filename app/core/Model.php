<?php 

class Model {
	private $Host;
	private $Cache;

	public function __construct() {
		global $config;
		
		$host_class = str_replace(" ", "_", ucwords($config["host"]));		
		$this->Host = new $host_class();
		$this->Cache = Cache::get_instance();
		
		if(method_exists($this->Host, "account_info")) {
			if(empty($this->Cache->account))
				$this->Cache->update("account", $this->Host->account_info());
		}
	}
	
	private function content_sort($a, $b) {
		if(!is_object($a))
			return 0;
		
		//if subpages exist, drill down
		if(!empty($a->subpages) && is_array($a->subpages))
			usort($a->subpages, array($this, "content_sort"));
		
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
			//set unachievable max age to avoid cache refreshes
			$item = $this->get_single("$path/$name", -1, false);
			$items[] = $item;
		}
		
		usort($items, array($this, "content_sort"));
			
		$this->Cache->update($type, $items);
		
		return $items;
	}
	
	public function get_single($path, $max_age=300, $cache=true, $thumb=false) {
		$path = trim_slashes($path);
		$key = "path";
		
		if($thumb)
			$type = "thumbs";
		else
			$type = array_shift(explode("/", $path));
		
		if($type == "pages") {
			$old_path = $path;
			$numbered_path = str_replace("/", "/(\d+\.\s*)?", $path); //match numbered pages in cache
			$path = $numbered_path;
		}
		
		if($type == "thumbs") {
			$old_path = $path;
			$new_path = $path."?size=".$thumb; //match numbered pages in cache
			$path = $new_path;
			$key = "url";
			
			//sanitize path for use in regex
			$path = preg_quote($path, "~");
		}
		
		$cache_route = array_search_recursive('~'.$path.'$~i', $this->Cache->$type, $key, true);
		$item = null;
		
		//if found cache, follow cache search route to get it
		if($cache_route) {
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
		}
		
		//if no cache or cache older than max age (default 5 mins), refresh
		if(!$cache_route || ($max_age >= 0 && $this->Cache->age($type) > $max_age)) {	
			if(isset($old_path))
				$path = $old_path; //return to original path for host fetch
		
			if($type == "thumbs")
				$item = $this->Host->get_file($path, $thumb);
			else
				$item = $this->Host->get_single($path, $item);
			
			if($item && $cache) {
				//replace old cache
				if(isset($cache_route[0])) {
					$old = &$this->Cache->$type;
					unset($old[$cache_route[0]]);
				}

				$this->Cache->add($type, $item);
				$this->Cache->save($type);
			}
		}
		
		return $item;
	}
	
	public function get_file($path, $thumb) {
		//if not thumb, serve from host
		if(!$thumb)
			return $this->Host->get_file($path, false);
		
		//cache thumbs for 20 mins
		return $this->get_single($path, 1200, true, $thumb);
	}
}