<?php

require_once "DropLib.php";

class Dropbox extends Cloud_Host {
	private $API;
	private $account;
	private $Cache;
	
	public function __construct() {
		parent::__construct();
				
		$consumer_key 		= "whoen0lsbfo9c6y";
		$consumer_secret 	= "jxto2adt35bmayy";
		
		$config_file = "app/config/dropbox.php";
		
		if(file_exists($config_file)) {
			//use saved tokens
			include_once $config_file;			
			$token_key 				= $tokens["token_key"];
			$token_secret 		= $tokens["token_secret"];
		}
		else {
			throw new Exception("NEED TO AUTH WITH DROPBOX!");
		}
		
		$this->API = new DropLib($consumer_key, $consumer_secret, $token_key, $token_secret);
		$this->API->setNoSSLCheck(true); //while developing locally
		
		$this->Cache = new Cache("account");
		if(empty($this->Cache->account))
			$this->Cache->update("account", $this->API->accountInfo());
	}
	
	private function encode_path($path) {
		return str_replace("%2F", "/", rawurlencode($path));
	}
	
	private function get_file_path($meta, $exts=null, $filename="") {
		if(!$exts)
			$exts = array_flatten($this->config["parsers"]);
		
		if(!$filename) {
			//look for '!' prefix
			$match = array_search_recursive('~/!.*$~', $meta, "path", true);
		}
		else {
			//sanitize title for use in regex
			$filename = preg_replace('/([{}\(\)\^$&.\*\?\/\+\|\[\\\\]|\]|\-)/', "\\1", $title);
		}
		
		//if not found, get first parsable file
		if(!$match)
			$match = array_search_recursive("~$filename\.(".implode("|", $exts).')$~i', $meta, "path", true);
		
		if(!$match)
			return false;
		
		return end($match);
	}
	
	public function dir_contents($path, $dirs_only=true) {
		$path = $this->encode_path($this->content_root."/".trim_slashes($path));
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
		$type = array_shift(explode("/", $path));
		$full_path = $this->encode_path($this->content_root."/$path");
		
		if(strpos($path, "/_") !== false)
			return false; //prevent access to a hidden folder
		
		try {
			$meta = $this->API->metadata($full_path);
		}
		catch(Exception $e) {
			return false;
		}
		
		$meta["published"] = $meta["created"] = $meta["modified"] = strtotime($meta["modified"]);
		
		if($type != "collections") {
			$meta["slug"] = basename($meta["path"]);
			
			$meta["permalink"] = $this->config["site_root"];
			if(!$this->config["pretty_urls"])
				$meta["permalink"] .= "/?";
				
			$permalink = "/".$path;
			
			if($type == "pages") {
				//remove order numbers from permalink
				$permalink = preg_replace('~/\d+\.\s*~', "/", $permalink);
				//remove /pages/ and /home/ from permalink if exist
				$permalink = preg_replace('~^/pages(/home$)?~i', "", $permalink);
			}
			
			$meta["permalink"] .= $permalink;
		
			$main_file = ($type == "snippet") ? $full_path : $this->get_file_path($meta);
			
			if($main_file) {
				$file_meta = ($type == "snippet") ? $meta : $this->API->metadata($this->encode_path($main_file));
	
				$meta["file_path"] = $main_file;
				$meta["title"] = $this->filename_from_path($main_file);
				$meta["extension"] = $this->ext_from_path($main_file);
				$meta["modified"] = strtotime($file_meta["modified"]);
				$meta["revision"] = $file_meta["revision"];
				
				//if no cache or cached content out of date
				if(!$Cache || $meta["modified"] > $Cache->modified) {				
					$meta["raw_content"] = $meta["content"] = $this->API->download($this->encode_path($main_file));
					
					$format = parent_key($this->config["parsers"], $meta["extension"]);
					
					if($meta["content"] && $format) {
						$parser_class = $format."_Parser";
						if(class_exists($parser_class)) {
							$Parser = new $parser_class();
							$meta["content"] = $Parser->parse($meta["content"]);
						}
					}
					
					$meta["content"] = $this->resolve_media_srcs($meta["content"], $path);
				}
				else {
					//cache is up-to-date
					$meta["raw_content"] = $Cache->raw_content;
					$meta["content"] = $Cache->content;
				}
			}
			
			if($type == "pages") {
				if(preg_match('~^(?<num>\d+)\.\s*(?<slug>.+)$~', $meta["slug"], $matches)) {
					$meta["order"] = $matches["num"];
					$meta["slug"] = $matches["slug"];
				}
				
				//recurse to get subpages
				foreach($meta["contents"] as $subpage) {
					if($subpage["is_dir"])
						$meta["subpages"][] = $this->get_single($path."/".basename($subpage["path"]));
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
		
		$type_class = ucfirst(substr($type, 0, -1)); //from lowercase plural
		
		$item = new $type_class($meta);
		return $item;
	}
	
	protected function get_file_url($path, $try_public=true) {
		$path = trim_slashes($path);
	
		if($try_public and strpos(strtolower($this->content_root), "public/") < 2)
			return "http://dl.dropbox.com/u/".$this->encode_path($this->Cache->account["uid"]."/Lando/".$this->config["site_title"]."/$path");
		else
			return $this->config["site_root"]."/file.php?path=".urlencode($path);
	}
	
	public function get_file($path, $thumb) {
		$path = $this->encode_path($this->content_root."/".trim_slashes($path));
		
		try {
			$meta = $this->API->metadata($path);
		}
		catch(Exception $e) {
			return false;
		}
		
		$File = $this->process_file($meta);

		if($thumb) {
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
					
			if($File->width && $File->height) {
				$dims = $this->calc_thumb_dims($thumb, $File->width, $File->height);
				$File->width = $dims["width"];
				$File->height = $dims["height"];
			}

			$File->dynamic_url .= "&amp;size=$thumb";
			$data_uri = $this->API->thumbnail($path, $thumb_codes[$thumb], "JPEG");
			$File->mime_type = stristr(stristr($data_uri, "image/"), ";", true);
			$File->raw_content = str_replace("data:".$File->mime_type.";base64,", "", $data_uri);
		}
		else
			$File->raw_content = $this->API->download($path);
		
		return $File;
	}
	
	private function process_file($file) {
		$rel_path = str_replace($this->config["host_root"]."/".$this->config["site_title"], "", $file["path"]);
					
		$file["modified"] = strtotime($file["modified"]);
		$file["title"] = $this->filename_from_path($file["path"]);
		$file["extension"] = $this->ext_from_path($file["path"]);
		$file["url"] = $this->get_file_url($rel_path);
		$file["dynamic_url"] = $this->get_file_url($rel_path, false);
		
		$file["order"] = $this->extract_order($file["title"]);
		
		if(strpos($file["mime_type"], "image") !== false) {
			$dims = $this->extract_dimensions($file["title"]);
			array_merge($dims, $file);
			
			$file = new Image($file);
		}
		else
			$file = new File($file);
			
		return $file;
	}
}