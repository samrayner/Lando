<?php

class Image extends File {
	public $width;
	public $height;
	public $thumbs;
	
	public function calc_thumbs() {
		$thumb_sizes = array(
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
		
		foreach($thumb_sizes as $size => $max) {
			$this->thumbs[$size] = array();
			$this->thumbs[$size]["url"] = $this->dynamic_url."&amp;size=$size";
			
			if($w = $this->width and $h = $this->height) {
				$ratio = $w/$h;
				
				switch($size) {
					//'cover' thumb scaling
					case "75": case "150":
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
				
				$this->thumbs[$size]["width"] = round($width);
				$this->thumbs[$size]["height"] = round($height);
			}	
		}
	}
}