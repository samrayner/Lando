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
		return '<a href="#">image</a>';
	}
	
/*
	public function thumb_html($size="150", $link=true) {
		if(!array_key_exists((string)$size, $this->thumbs))
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
*/
}