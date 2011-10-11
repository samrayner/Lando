<?php 

class Model {
	private $Host;
	private $Cache;
	private $config;

	public function __construct() {
		global $config;
		$this->config = $config;
		
		$host_class = str_replace(" ", "_", ucwords($config["host"]));		
		$this->Host = new $host_class();
		$this->Cache = new Cache();
	}
	
	public function get_host_info() {
		if(method_exists($this->Host, "account_info")) {
			if(empty($this->Cache->account) || $this->Cache->age("account") > 86400) //24hrs
				$this->Cache->update("account", $this->Host->account_info());
		}
		
		return $this->Cache->account;
	}
	
	private function sort_content($a, $b) {
		if(!is_object($a))
			return 0;
		
		//if subpages exist, drill down
		if(!empty($a->subpages) && is_array($a->subpages))
			usort($a->subpages, array($this, "sort_content"));
		
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
	
	private function sort_pages($pages, $order=null) {
		if(!$order)
			$order = $this->config["page_order"];
			
		$sorted = array();
		
		foreach($order as $slug => $suborder) {	
			if($slug != "_hidden") {
				$search_route = array_search_recursive($slug, $pages, "slug");
				
				if(isset($search_route[0])) {
					$i = sizeof($sorted);
				
					$sorted[$i] = $pages[$search_route[0]];
				
					//sort subpages
					if(!empty($sorted[$i]->subpages) && is_array($sorted[$i]->subpages))
						$sorted[$i]->subpages = $this->sort_pages($sorted[$i]->subpages, $suborder);
					
					//unset if inserted into order from config setting
					unset($pages[$search_route[0]]);
				}
			}
		}
		
		//append pages that don't appear in page_order config
		return array_merge($sorted, $pages);
	}
	
	public function get_all($path, $max_age=300) {
		$path = trim_slashes($path);
		$path_segs = explode("/", trim_slashes($path));
		$type = $path_segs[0];
		
		$collection_files = ($type == "collections" and count($path_segs) > 1);
		
		if($type == "snippets" || $collection_files)
			$dirs_only = false;
		else
			$dirs_only = true;
	
		//if cache younger than max age (default 5 mins) then use it to list items
		/*
		if($this->Cache->age($type) < $max_age)
					$names = $this->Cache->top_level($type);
				else
		*/
			$names = $this->Host->dir_contents($path, $dirs_only);
		
		$items = array();
		
		foreach($names as $name) {
			//set unachievable max age to avoid cache refreshes
			$item = $this->get_single("$path/$name", false, -1);
			
			if($item)
				$items[] = $item;
		}
		
		usort($items, array($this, "sort_content"));
		
		if($type == "pages")
			$items = $this->sort_pages($items);
		
		//bulk overwrite of cache
		$this->Cache->update($type, $items);
		
		return $items;
	}
	
	public function get_single($path, $recache=true, $max_age=300, $thumb=false) {
		$path = trim_slashes($path);
		$key = "path";
		$old_path = $path;
		
		$type = ($thumb) ? "thumbs" : array_shift(explode("/", $path));
		
		if($type == "thumbs") {
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
				//if content use object notation, otherwise array notation
				if(is_object($item))
					$item = $item->$next_key;
				elseif(is_array($item))
					$item = $item[$next_key];
			}
		}
		
		//if no cache or cache older than max age (default 5 mins), refresh
		if(!$cache_route || ($max_age >= 0 && $this->Cache->age($type) > $max_age)) {	
			$path = $old_path; //return to original path for host fetch
		
			if($type == "thumbs")
				$item = $this->Host->get_file($path, $thumb);
			else
				$item = $this->Host->get_single($path, $item);
			
			if($item && $recache) {
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
		return $this->get_single($path, true, 1200, $thumb);
	}
}