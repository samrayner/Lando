<?php

require_once "DropLib.php";

class Dropbox extends Cloud_Host {
	private $API;
	
	public function __construct() {
		parent::__construct();
				
		$params = array( 
			'consumerKey' 		=> 'whoen0lsbfo9c6y', 
			'consumerSecret' 	=> 'jxto2adt35bmayy', 
			'sslCheck' 				=> false //while developing locally
		);
		
		$config_file = "app/config/dropbox.php";
		
		if(include_exists($config_file)) {
			//use saved tokens
			include_once $config_file;			
			$params["tokenKey"] 		= $tokens["token_key"];
			$params["tokenSecret"] 	= $tokens["token_secret"];
		}
		else {
			throw new Exception("NEED TO AUTH WITH DROPBOX!");
		}
		
		$this->API = new DropLib($params);
	}
	
	public function account_info() {
		return $this->API->accountInfo();
	}
	
	private function get_file_path($meta, $exts=null, $filename="") {
		if(!$exts)
			$exts = array_flatten($this->config["parsers"]);
		
		array_push($exts, "html", "htm");
		
		if(!$filename) {
			//look for '!' prefix
			$match = array_search_recursive('~/!.*$~', $meta, "path", true);
		}
		else {
			//sanitize title for use in regex
			$filename = preg_quote($filename, "~");
		}
		
		//if not found, get first parsable file
		if(!$match)
			$match = array_search_recursive("~$filename\.(".implode("|", $exts).')$~i', $meta, "path", true);
		
		if(!$match)
			return false;
		
		return end($match);
	}
	
	public function dir_contents($path, $dirs_only=true) {
		$path = $this->config["host_root"]."/".trim_slashes($path);
		$meta = $this->API->metadata($path);
		
		$items = array();
		
		foreach($meta["contents"] as $item) {
			if($dirs_only == $item["is_dir"])
				$name = basename($item["path"]);
				if(strpos($name, "_") !== 0) //check dir is not hidden
					$items[] = $name;
		}
		
		return $items;
	}
	
	public function get_single($path, $Cache=null) {
		$path = trim_slashes($path);
		$type = array_shift(explode("/", $path));
		$type_class = ucfirst(substr($type, 0, -1)); //from lowercase plural
		
		//prevent access to misc and hidden folders
		if(!class_exists($type_class) || strpos($path, "/_") !== false)
			return false;
		
		$meta = array();
		
		if($Cache)
			$meta = $Cache->export();
		
		$full_path = $this->config["host_root"]."/$path";
		
		if(in_array($type, array("pages","posts","drafts"))) {
			$old_path = $full_path;
			$path = $this->sanitize_path($path);
			$full_path = $this->sanitize_path($full_path);
			
			//if slug has changed
			if($full_path != $old_path) {
				try {
					//rename directory
					$latest = $this->API->move($old_path, $full_path);
				}
				//if directory exists
				catch(Exception $e) {
					$attempt = 1;
				
					//until successful rename
					while(!isset($latest)) {
						try {
							//rename with 1..4 appended
							$latest = $this->API->move($old_path, $full_path."-$attempt");
						}
						catch(Exception $e) {
							//if still name clash, increase number and retry
							$attempt++;
							//if trying for a 5th time, fail
							if($attempt == 5)
								return false;
						}
					}
				}
			}
		}
		
		try {
			//if not successfully renamed slug directory
			if(!isset($latest))
				$latest = $this->API->metadata($full_path);
		}
		catch(Exception $e) {
			return false;
		}
		
		if(isset($latest["is_deleted"]) && $latest["is_deleted"])
			return false;
		
		//update cache with latest metadata if exists
		$meta = array_merge($meta, $latest);
		
		$meta["published"] = $meta["created"] = $meta["modified"] = strtotime($meta["modified"]);
		
		if($type != "collections") {
			$meta["slug"] = basename($meta["path"]);
			
			$permalink = "/$path/";
			
			if($type == "pages") {
				//remove /pages/ and /home/ from permalink if exist
				$permalink = preg_replace('~^/pages(/home/$)?~i', "", $permalink);
			}
			
			$meta["permalink"] = $permalink;
		
			$main_file = ($type == "snippet") ? $full_path : $this->get_file_path($meta);
			
			if($main_file) {
				$file_meta = ($type == "snippet") ? $meta : $this->API->metadata($main_file);
	
				$meta["file_path"] = $main_file;
				$meta["title"] = $this->filename_from_path($main_file);
				$meta["extension"] = $this->ext_from_path($main_file);
				$meta["format"] = parent_key($this->config["parsers"], $meta["extension"]);
				$meta["modified"] = strtotime($file_meta["modified"]);
				
				//if no cache or cached content out of date
				if(!$Cache || $meta["modified"] > $Cache->modified) {
					//download raw content
					$meta["raw_content"] = $this->API->download($main_file);
				}
				
				//scrape for manually set metadata and add
				$meta = array_merge($meta, $this->manual_meta($meta["raw_content"]));
			}

			if($type == "pages") {
				$meta["subpages"] = array();
			
				//recurse to get subpages
				foreach($meta["contents"] as $subpage) {
					if($subpage["is_dir"]) {
						$page = $this->get_single("$path/".basename($subpage["path"]));
						
						if($page)					
							$meta["subpages"][] = $page;
					}
				}
			}
		}
		else { //collection
			$meta["title"] = basename($meta["path"]);
			
			//if no cache or cached content out of date
			if(!$Cache || $meta["modified"] > $Cache->modified) {		
				$files = array();
				
				foreach($meta["contents"] as $file) {
					if(!$file["is_dir"])
						$files[] = $this->process_file($file);
				}
			}
			else {
				//cache is up-to-date
				$files = $Cache->files;
			}
			
			$meta["files"] = $files;
		}
		
		$item = new $type_class($meta);
		
		return $item;
	}
	
	public function get_file($path, $thumb) {
		$thumb_codes = array(
			"icon" 	=> "16x16", 
			"64" 		=> "64x64", 
			"75"		=> "75x75_fit_one",
			"150" 	=> "150x150_fit_one",
			"s" 		=> "320x240_bestfit",
			"m" 		=> "480x320_bestfit", 
			"l" 		=> "640x480_bestfit",
			"xl" 		=> "960x640_bestfit",
			"xxl" 	=> "1024x768_bestfit"
		);
		
		if($thumb && !isset($thumb_codes[$thumb]))
			return false;
	
		$path = $this->config["host_root"]."/".trim_slashes($path);
		
		try {
			$meta = $this->API->metadata($path);
		}
		catch(Exception $e) {
			return false;
		}
		
		$File = $this->process_file($meta);

		if($thumb && $meta["thumb_exists"]) {
			$File->url .= "?size=$thumb";
			$File->raw_content = $this->API->thumbnail($path, $thumb_codes[$thumb], "JPEG");
			$File->mime_type = "image/jpeg";
			$File->extension = "jpg";
			$File->resize($thumb); //calculate thumb dimensions and replace
		}
		else
			$File->raw_content = $this->API->download($path);
		
		return $File;
	}
	
	private function process_file($file) {
		$rel_path = str_replace($this->config["host_root"], "", $file["path"]);
					
		$file["modified"] = strtotime($file["modified"]);
		$file["title"] = $this->filename_from_path($file["path"]);
		$file["extension"] = $this->ext_from_path($file["path"]);
		$file["url"] = $this->get_file_url($rel_path);
		
		$file["order"] = $this->extract_order($file["title"]);
		
		if(strpos($file["mime_type"], "image") !== false) {
			$dims = $this->extract_dimensions($file["title"]);
			$file = array_merge($file, $dims);
			
			$file = new Image($file);
		}
		else
			$file = new File($file);
			
		return $file;
	}
}