<?php

class Image extends File {
	public $width;
	public $height;
	public $thumbs;
	
	public function __toString() {
		return $this->html();
	}
	
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
	
	public function html() {
		$html = '<img src="'.$this->url.'" alt="'.$this->title.'"';
		
		if($this->width)
			$html .= ' width="'.$this->width.'"';
		
		if($this->height)
			$html .= ' height="'.$this->height.'"';
		
		$html .= ' />';
		
		return $html;
	}
	
	public function thumb_html($size="150", $link=true) {
		if(!array_key_exists((string)$size, $thumbs))
			$size = "150";
	
		$html = "";
	
		if($link)
			$html .= '<a href="'.$this->url.'">';
		
		$html .= '<img src="'.$this->thumbs[$size]["url"].'" alt="'.$this->title.'"';
		
		if(isset($this->thumbs[$size]["width"]))
			$html .= ' width="'.$this->thumbs[$size]["width"].'"';
		
		if(isset($this->thumbs[$size]["height"]))
			$html .= ' height="'.$this->thumbs[$size]["height"].'"';
		
		$html .= ' />';
		
		if($link)
			$html .= '</a>';
		
		return $html;
	}
}