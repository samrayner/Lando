<?php

class Image extends File {
	public $width;
	public $height;
	
	private static $thumb_sizes = array(
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
	
	public function __toString() {
		return $this->html();
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
		if(!array_key_exists((string)$size, self::$thumb_sizes))
			$size = "150";
		
		$this->resize($size);
	
		$html = "";
	
		if($link)
			$html .= '<a href="'.$this->url.'">';
		
		$html .= '<img src="'.$this->dynamic_url.'?size='.$size.'" alt="'.$this->title.'"';
		
		if(isset($this->width))
			$html .= ' width="'.$this->width.'"';
		
		if(isset($this->height))
			$html .= ' height="'.$this->height.'"';
		
		$html .= ' />';
		
		if($link)
			$html .= '</a>';
		
		return $html;
	}

	public function resize($code) {
		$w = $this->width;
		$h = $this->height;
		
		if(!$w || !$h)
			return false;
		
		$max = self::$thumb_sizes[$code];
		$ratio = $w/$h;
		
		switch($code) {
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
		
		$this->width 	= round($width);
		$this->height = round($height);
		
		return true;
	}
}