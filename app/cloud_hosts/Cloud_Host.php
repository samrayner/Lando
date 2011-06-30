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
  
 	protected function extract_dimensions(&$title) {
		if(preg_match('~[^0-9a-z](?<w>[1-9]\d{0,4})x(?<h>[1-9]\d{0,4})(?:\W|$)~i', $title, $matches)) {
			$dims["width"] = $matches["w"];
			$dims["height"] = $matches["h"];
			$title = trim(str_replace($matches[0], " ", $title));
		}
	
		return $dims;
	}
	
	protected function extract_order(&$title) {
		$order = null;
	
		if(preg_match('~^(?<num>\d+)+\.\s*(?<title>.+)$~', $title, $matches)) {
			$order = $matches["num"];
			$title = $matches["title"];
		}
		
		return $order;
	}
	
	protected function calc_thumb_dims($code, $w, $h) {
		$sizes = array(
			"icon" 	=> array("width" => 16, 	"height" => 16), 
			"64" 		=> array("width" => 64, 	"height" => 64), 
			"75"		=> array("width" => 75, 	"height" => 75),
			"150" 	=> array("width" => 150, 	"height" => 150),
			"s" 		=> array("width" => 320, 	"height" => 240),
			"m" 		=> array("width" => 480, 	"height" => 320), 
			"l" 		=> array("width" => 640, 	"height" => 480),
			"xl" 		=> array("width" => 960, 	"height" => 640),
			"xxl" 	=> array("width" => 1024, "height" => 768)
		);
		
		$max = $sizes[$code];
		$ratio = $w/$h;
		
		switch($size) {
			//'cover' thumb scaling
			case "75": 
			case "150":
				if($w >= $h) { //wide or square
					$height = $max["height"];
					$width = $height*$ratio;
				}
				if($w < $h) { //tall
					$width = $max["width"];
					$height = $width/$ratio;
				}
				break;
			//'contain' thumb scaling
			default: 
				$wScale = $max["width"]/$w;
				
				if($h*$wScale >= $max["height"]) { //too tall or perfect
					$height = $max["height"];
					$width = $height*$ratio;
				}
				else { //too wide
					$width = $max["width"];
					$height = $width/$ratio;
				}
		}
		
		$dims["width"] 	= round($width);
		$dims["height"] = round($height);
		
		return $dims;
	}
}