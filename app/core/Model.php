<?php 

class Model {
	private $Host;
	private $Cache;
	private $config;
	
	const RECACHE_LIMIT = 1;
	private $recache_count = 0;

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
		
		foreach($order as $slug => $_) {	
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
	
	public function get_all($path, $max_age=3600) {
		$path = trim_slashes($path);
		$path_segs = explode("/", trim_slashes($path));
		$type = $path_segs[0];
		
		$collection_files = ($type == "collections" && count($path_segs) > 1);
		
		if($type == "snippets" || $collection_files)
			$dirs_only = false;
		else
			$dirs_only = true;
		
		$pages = ($type == "pages");
		
		$names = $this->Cache->dir_contents($path, $pages, $collection_files);

		//if only current page has just been cached (current done before nav)
    //OR cache older than max age (default 1 hour), refresh
    $age = $this->Cache->age($path);
    $same_load = 2;
    
		//Update cached list if:
		//a) 	Folder has only just been created so may not be not complete OR
		//b) 	i) Cache is older than max age (default 1 hour) AND
		//		ii)There hasn't been another cache on this page load
		$should_cache = $age < $same_load || ($max_age >= 0 && $age > $max_age &&
																					$this->recache_count < self::RECACHE_LIMIT);

    if($should_cache) {
    	$this->Cache->touch($path);

    	$this->connect_host();
    	$cached_names = $names;
			$names = $this->Host->dir_contents($path, $dirs_only);

			$deleted = array_diff($cached_names, $names);

			foreach($deleted as $name) {
				$this->Cache->delete("$path/$name");
				$this->Cache->delete("files/$path/$name");
			}

			$this->recache_count++;
		}
		
		$items = array();
		
		foreach($names as $name) {
			if($collection_files)
				$Item = $this->get_file("$path/$name", false, -1); //not thumb and don't recache individually before sort
			else
				$Item = $this->get_single("$path/$name", -1); //unachievable max-age to stop individual recaches before sort
			
			if($Item)
				$items[] = $Item;
		}
		
		if(!empty($items)) {
			usort($items, array($this, "sort_content"));
		
			if($pages) {
				$page_order = $this->config["page_order"];
				array_shift($path_segs);

				foreach($path_segs as $page) {
					if(isset($page_order[$page]))
						$page_order = $page_order[$page];
				}

				$items = $this->sort_pages($items, $page_order);
			}
		}
		
		return $items;
	}
	
	public function get_single($path, $max_age=86400) {
		$cache_path = $path = trim_slashes($path);
		$type = array_shift(explode("/", $path));
		
		if($type == "pages")
			$cache_path .= "/page";
		
		$Item = $this->Cache->get_single($cache_path);
		
		//Update cache if:
		//a) 	Item doesn't exist in cache yet OR
		//b) 	i)	Cache is older than max age (default 1 hour) AND
		//		ii)	Recache limit hasn't been exceeded yet
		$should_cache = !$Item || ($max_age >= 0 && $this->Cache->age($cache_path) > $max_age && 
															 $this->recache_count < self::RECACHE_LIMIT);
		
		if($should_cache) {
			$this->connect_host();
			$Item = $this->Host->get_single($path, $Item);
			
			if($Item)
				$this->Cache->update($cache_path, $Item);
			
			$this->recache_count++;
		}
		
		if(!$Item)
			return false;
		
		if($type == "pages")
			$Item->subpages = $this->get_all($path);
			
		if($type == "collections")
			$Item->files = $this->get_all($path);
		
		return $Item;
	}

	private function get_thumb_ext($path) {
		$ext = ext_from_path($path);
		return in_array($ext, array("png", "gif")) ? "png" : "jpg";
	}
	
	public function get_file($path, $thumb_size, $max_age=14000) {
		$path = trim_slashes($path);
		$cache_path = "files/".$path;
		
		$Item = $this->Cache->get_single($cache_path);

		$cached_mod = $Item ? $Item->modified : 0;
		
		//Update cache if:
		//a) 	Thumb doesn't exist in cache yet OR
		//b) 	i)	Cache is older than max age (default 3.8 hours - media link expiration)
		//		ii) Recache limit hasn't been exceeded yet
		$should_cache = !$Item || 
										(($max_age >= 0 && $this->Cache->age($cache_path) > $max_age) && 
										$this->recache_count < self::RECACHE_LIMIT);

		if($should_cache) {
			$this->connect_host();
			$Item = $this->Host->get_file($path, $thumb_size);
			
			if($Item)
				$this->Cache->update($cache_path, $Item);
			
			$this->recache_count++;
		}

		$updated_mod = $Item ? $Item->modified : 0;
		
		//if image, try to cache the file
		if($Item && get_class($Item) == "Image") {
			if($thumb_size) {
				$Item->extension = $this->get_thumb_ext($path);
				$cache_path = preg_replace('~\.\w+$~', ".$thumb_size.".$Item->extension, $cache_path);
			}

			$full_path = $this->Cache->full_path($cache_path);
		
			//recache if doesn't exist in cache yet or image metadata has changed
			if(!file_exists($full_path) || $updated_mod > $cached_mod) {
				$this->connect_host();				
				$image_code = $this->Host->get_image($path, $thumb_size);
				
				if($image_code)
					$this->Cache->update($cache_path, $image_code);
				
				$this->recache_count++;
			}
			
			if(!file_exists($full_path))
				return false;

			$dims = getimagesize($full_path);
			$Item->width = $dims[0];
			$Item->height = $dims[1];
			$Item->modified = filemtime($full_path);

			$url = str_replace(dirname(dirname(dirname(__FILE__))), "", $full_path);
			$Item->url = $this->config["site_root"].str_replace(array('%2F','~'), array('/','%7E'), rawurlencode($url));
		}
		
		return $Item;
	}
	
	public function install_content($local_path, $host_path="", $log_file=false) {
		$exec_start = time();
		
		if($log_file && file_exists($log_file))
			unlink($log_file);
		
		$log = "Installation started ".date("F jS, Y \a\t G:i:s")."";
		
		//count all files and folders
		$file_count = 0;
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($local_path), RecursiveIteratorIterator::SELF_FIRST);
		foreach($files as $name => $_) {
			if(substr(basename($name), 0, 1) != ".")
				$file_count++;
		}
		
		$host_segs = explode("/", trim_slashes($host_path));
		$path = "";
		$nest = 0;
		
		$file_count += count($host_segs);
		$log .= "\n\nUploading $file_count files and folders...\n";
		
		if($log_file)
			@file_put_contents($log_file, $log);
		
		$this->connect_host();
		
		foreach($host_segs as $folder) {
			$path .= $folder."/";
			$nest = sizeof(explode("/", trim_slashes($path)))-1;
			$log = str_repeat("\t", $nest);
		
			try {
				$this->Host->create_dir($path);
				$log .= 'Creating folder "'.$folder.'"';
			}
			catch(DropLibException_API $e) {
				$log .= 'Folder "'.$folder.'" already exists';
			}
			
			if($log_file)
				@file_put_contents($log_file, "\n$log", FILE_APPEND);
		}
		
		$this->put_content($local_path, $host_path, $log_file, $file_count);
		
		$exec_end = time();
		
		@file_put_contents($log_file, "\n\nInstallation complete (in ".($exec_end-$exec_start)." seconds).", FILE_APPEND);
	}
	
	private function put_content($local_path, $host_path="", $log_file=false) {
		$local_path = "/".trim_slashes($local_path);
		$host_path = trim_slashes($host_path);
	
		foreach(glob("$local_path/*") as $file) {
			$nest = sizeof(explode("/", $host_path));
			$log = str_repeat("\t", $nest);
		
			if(is_dir($file)) {
				$folder = basename($file);
		
				try {
					$this->Host->create_dir($host_path."/".$folder);
					$log .= 'Creating folder "'.$folder.'"';
				}
				catch(DropLibException_API $e) {
					$log .= 'Folder "'.$folder.'" already exists';
				}
				
				if($log_file)
					@file_put_contents($log_file, "\n$log", FILE_APPEND);

				$this->put_content($file, "$host_path/$folder", $log_file);
			}
			else {
				$log .= 'Uploading "'.basename($file).'"';
				
				if($log_file)
					@file_put_contents($log_file, "\n$log", FILE_APPEND);
				
				$this->Host->upload("/$host_path", $file);
			}
		}
		
		return true;
	}

	public function publish_draft($slug) {
		$this->connect_host();
		$meta = $this->Host->move("drafts/$slug", "posts/$slug", 5);

		if(!$meta)
			return false;

		$this->Cache->delete("drafts/$slug");

		return basename($meta["path"]);
	}
}