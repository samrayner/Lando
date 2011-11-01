<?php

class Image extends File {
	public $width;
	public $height;
	
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
		global $Lando;
		$rel_path = str_replace($Lando->config["host_root"], "", $this->path);
		$Thumb = $Lando->get_file($rel_path, $size);
		
		if(!$Thumb)
			return false;
	
		$html = "";
	
		if($link)
			$html .= '<a href="'.$this->url.'">';
		
		$html .= '<img src="'.$Thumb->url.'" alt="'.$this->title.'"';
		
		if(isset($Thumb->width))
			$html .= ' width="'.$Thumb->width.'"';
		
		if(isset($Thumb->height))
			$html .= ' height="'.$Thumb->height.'"';
		
		$html .= ' />';
		
		if($link)
			$html .= '</a>';
		
		return $html;
	}
	
	//get functions
	
	public function width() {
		return $this->width;
	}

	public function height() {
		return $this->height;
	}
}