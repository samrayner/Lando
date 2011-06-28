<?php 

abstract class Cloud_Host {
	protected $content_root;
	protected $parsable_exts;
	
	public function __construct() {
		global $config;
		$this->config = $config;
		$this->content_root = $config["host_root"]."/".$config["site_title"];
	}

	protected function filename_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_FILENAME);
	}

	protected function ext_from_path($path) {
		return pathinfo(trim_slashes($path), PATHINFO_EXTENSION);
	}
	
	protected function get_file_url($path) {
		return $this->config["site_root"]."/get_file.php?file=".urlencode(trim_slashes($path));
	}
	
	protected function resolve_media_srcs($html, $dir) {
		if(preg_match_all('/<(?:img|audio|video|source)[^>]+src="([^"]*)"[^>]*>/i', $html, $tags)) {
			foreach($tags[1] as $src) {
				//if relative url
				if(strpos($src, ":") === false && strpos($src, "/get_file.php") === false) {
					if(strpos($src, "/") === 0)
						$resolved = $this->get_file_url(substr($src, 1)); //resolve relative to site root
					else {
						$src_segs = explode("/", trim_slashes($src));
						$dir_segs = explode("/", trim_slashes($dir));
						
						while(isset($src_segs[0]) && $src_segs[0] == "..") {
							array_pop($dir_segs); //go up one dir
							array_shift($src_segs); //move on to next segment
						}
						
						$dir 			= implode("/", $dir_segs);
						$resolved = implode("/", $src_segs);
						
						$resolved = preg_replace('~^./~', '', $resolved);
						
						$resolved = $this->get_file_url($dir."/$resolved"); //resolve to current dir
					}

					$html = str_replace('"'.$src.'"', '"'.$resolved.'"', $html);
				}
			}
		}
		
		return $html;
  }
}