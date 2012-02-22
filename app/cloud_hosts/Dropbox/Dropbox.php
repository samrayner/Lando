<?php

require_once "DropLib.php";

class Dropbox extends Cloud_Host {
	private $API;
	
	public function __construct($config) {
		parent::__construct($config);
		
		$params = array( 
			'consumerKey' 		=> 'trnjsiw5jeym92c', 
			'consumerSecret' 	=> 'sszl42e3d135d1t', 
			'sslCheck' 				=> true //while developing locally
		);
		
		$config_file = "app/config/dropbox.php";
		
		if(include_exists($config_file)) {
			//use saved token
			include $config_file;
			
			if(isset($oauth["token"]["key"], $oauth["token"]["secret"])) {
				$params["tokenKey"] 		= $oauth["token"]["key"];
				$params["tokenSecret"] 	= $oauth["token"]["secret"];
			}
		}
	
		$this->API = new DropLib($params);
	}
	
	public function create_dir($path) {
		return $this->API->createFolder($path);
	}
	
	public function upload($path, $file, $overwrite=true) {
		return $this->API->upload($path, $file, $overwrite);
	}

	public function move($old_path, $new_path, $attempt_limit) {
		$old_path = $this->config["host_root"]."/".trim_slashes($old_path);
		$new_path = $this->config["host_root"]."/".trim_slashes($new_path);

		$meta = false;

		try {
			//rename directory
			$meta = $this->API->move($old_path, $new_path);
		}
		//if directory exists
		catch(Exception $e) {
			$attempt = 1;
		
			//until successful rename
			while(!$meta) {
				try {
					//rename with 1..4 appended
					$meta = $this->API->move($old_path, $new_path."-$attempt");
				}
				catch(Exception $e) {
					//if still name clash, increase number and retry
					$attempt++;
					//if trying for a 5th time, fail
					if($attempt > $attempt_limit)
						return false;
				}
			}
		}

		return $meta;
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
		$items = array();
		
		try {
			$meta = $this->API->metadata($path, true);
		}
		catch(Exception $e) {
			return $items;
		}
		
		foreach($meta["contents"] as $item) {
			if($dirs_only == $item["is_dir"]) {
				$name = basename($item["path"]);
				if(strpos($name, "_") !== 0) //check dir is not hidden
					$items[] = $name;
			}
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
		
		$meta = array("hash"=>0);
		
		if($Cache)
			$meta = $Cache->export();

		$latest = array();
		
		if(in_array($type, array("pages","posts","drafts"))) {
			$old_path = $path;
			$path = $this->sanitize_path($path);
			
			//if slug has changed
			if($path != $old_path) {
				$latest = $this->move($old_path, $path, 5);

				//if failed to rename because of conflicts
				if(!$latest)
					return false;
			}
		}
		
		$full_path = $this->config["host_root"]."/$path";

		try {
			//if not renamed slug directory
			if(!$latest) {
				$new_meta = $this->API->metadata($full_path, true, $meta["hash"]);

				//if not "403 - not modified"
				if($new_meta !== true)
					$latest = $new_meta;
			}
		}
		catch(Exception $e) {
			return false;
		}
		
		if(isset($latest["is_deleted"]) && $latest["is_deleted"])
			return false;
		
		//update cache with latest metadata if exists
		$meta = array_merge($meta, $latest);
		
		$meta["published"] = $meta["created"] = $meta["modified"] = strtotime($meta["modified"]);
		
		if($type == "collections")
			$meta["title"] = basename($meta["path"]);
		else {
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
				$meta["title"] = filename_from_path($main_file);
				$meta["extension"] = ext_from_path($main_file);
				$meta["format"] = parent_key($this->config["parsers"], $meta["extension"]);
				$meta["modified"] = strtotime($file_meta["modified"]);
				
				//if no cache or cached content out of date
				if(!$Cache || $meta["modified"] > $Cache->modified) {
					//download raw content
					try {
						$meta["raw_content"] = $this->API->download($main_file);
					}
					catch(Exception $e) {
						$meta["raw_content"] = "";
					}
					
					//scrape for manually set metadata and add
					$meta["manual_metadata"] = $this->manual_meta($meta["raw_content"]);
				}
				
				//apply manual metadata overrides
				$meta = array_merge($meta, $meta["manual_metadata"]);
			}
		}
		
		$item = new $type_class($meta);
		
		return $item;
	}
	
	public function get_file($path, $thumb) {
		$path = $this->config["host_root"]."/".trim_slashes($path);
		
		try {
			$meta = $this->API->metadata($path);
		}
		catch(Exception $e) {
			return false;
		}
		
		$File = $this->process_file($meta);
	
		return $File;
	}
	
	private function get_thumb_format($path) {
		$ext = ext_from_path($path);
		return in_array($ext, array("png", "gif")) ? "PNG" : "JPEG";
	}

	public function get_image($path, $thumb_size=false) {
		$thumb_codes = array(
			"16" 		=> "16x16",
			"32" 		=> "small",
			"64" 		=> "s", 
			"128" 	=> "m",
			"s" 		=> "320x240_bestfit",
			"m" 		=> "480x320_bestfit", 
			"l" 		=> "l",
			"xl" 		=> "960x640_bestfit",
			"xxl" 	=> "xl"
		);

		$path = $this->config["host_root"]."/".trim_slashes($path);
		
		//if not getting thumbnail
		if(!isset($thumb_codes[$thumb_size])) {
			try {
				return $this->API->download($path);
			}
			catch(Exception $e) {
				return false;
			}
		}
		
		//if getting thumbnail
		try {
			$thumb = $this->API->thumbnail($path, $thumb_codes[$thumb_size], $this->get_thumb_format($path));
		}
		catch(Exception $e) {
			return false;
		}
		
		return base64_decode($thumb);
	}
	
	private function process_file($file) {
		$file["modified"] = strtotime($file["modified"]);
		$file["title"] = filename_from_path($file["path"]);
		$file["extension"] = ext_from_path($file["path"]);
		$file["order"] = $this->extract_order($file["title"]);
		
		try {
			$media = $this->API->media($file["path"]);
			$file["url"] = $file["original_url"] = $media["url"];
		}
		catch(Exception $e) {}
		
		if(strpos($file["mime_type"], "image") !== false) {
			//$dims = $this->extract_dimensions($file["title"]);
			//$file = array_merge($file, $dims);
			
			$file = new Image($file);
		}
		else
			$file = new File($file);
			
		return $file;
	}
	
	//oAuth functions
	public function request_token() {
		return $this->API->requestToken();
	}
	
	public function authorize_url($callback) {
		return $this->API->authorizeUrl($callback);
	}
	
	public function access_token() {
		return $this->API->accessToken();
	}
}