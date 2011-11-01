<?php 

class Model {
	private $Host;
	private $Cache;
	private $config;

	public function __construct() {
		global $config;
		$this->config = $config;
		$this->Cache = new Cache();
	}
	
	private function connect_host() {
		if(!$this->Host) {
			$host_class = str_replace(" ", "_", ucwords($this->config["host"]));
			$this->Host = new $host_class($this->config);
		}
	}
	
	public function get_host_info() {
		$this->connect_host();
	
		if(method_exists($this->Host, "account_info")) {
			if(!$this->Cache->get_single("account") || $this->Cache->age("account") > 86400) //24hrs
				$this->Cache->update("account", $this->Host->account_info());
		}
		
		return $this->Cache->get_single("account");
	}
	
	private function sort_content($a, $b) {
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
				$pub_cmp = $b->published - $a->published; //descending timestamp (newest first)
				return $pub_cmp ? $pub_cmp : strnatcmp($a->title, $b->title); //if same publish time, sort by title
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
					$sorted[] = $pages[$search_route[0]];
					
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
		
		$collection_files = ($type == "collections" && count($path_segs) > 1);
		
		if($type == "snippets" || $collection_files)
			$dirs_only = false;
		else
			$dirs_only = true;
		
		$pages = ($type == "pages");
		$names = $this->Cache->dir_contents($path, $pages);

    //if same page load or cache older than max age (default 5 mins), refresh
    $age = $this->Cache->age($path);
    $same_load = 5;
    
    if($age < $same_load || $age > $max_age) {    
    	$this->Cache->touch($path);
    	$this->connect_host();
			$names = $this->Host->dir_contents($path, $dirs_only);
		}
		
		$items = array();
		
		foreach($names as $name) {
			//unachievable max-age to stop recache of all
			$item = $this->get_single("$path/$name", -1);
			
			if($item)
				$items[] = $item;
		}
		
		if(!empty($items)) {
			usort($items, array($this, "sort_content"));
		
			if($pages)
				$items = $this->sort_pages($items);
		}
		
		return $items;
	}
	
	public function get_single($path, $max_age=300) {
		$cache_path = $path = trim_slashes($path);
		$type = array_shift(explode("/", $path));
		
		if($type == "pages")
			$cache_path .= "/data";
		
		$item = $this->Cache->get_single($cache_path);
		
		//if no cache or cache older than max age (default 5 mins), refresh
		if(!$item || ($max_age >= 0 && $this->Cache->age($cache_path) > $max_age)) {		
			$this->connect_host();
			$item = $this->Host->get_single($path, $item);
			
			if($item)
				$this->Cache->update($cache_path, $item);
		}
		
		if($type == "pages")
			$item->subpages = $this->get_all($path);
		
		return $item;
	}
	
	public function get_file($path, $thumb) {
		$cache_path = $path = trim_slashes($path);
		
		if($thumb) {
			$ext = pathinfo($path, PATHINFO_EXTENSION);
			$cache_path = str_replace($ext, "$thumb.$ext", $path);
		}
		
		$item = $this->Cache->get_file($cache_path);
		
		//if no cache or cache older than 4 hours (media link expiration) refresh
		if(!$item || $this->Cache->age($cache_path) > 60*60*4) {		
			$this->connect_host();
			$item = $this->Host->get_file($path, $thumb);
			
			if($item)
				$this->Cache->update($cache_path, $item);
		}
		
		return $item;
	}
}
