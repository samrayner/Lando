<?php

class Collection extends Content {
	public $files = array(); //of type File
	public $created;
	
	public function __toString() {
		return $this->list_html();
	}
	
	public function list_html($limit=0) {
		if($limit < 1 || $limit > count($this->files))
			$limit = count($this->files);
	
		$html = '<ul>';
		
		$i = 0;
		
		while($i < $limit) {
			$html .= "<li>$file</li>";
			$i++;
		}
			
		$html .= '</ul>';
		
		return $html;
	}

	public function image_list_html($type="gallery", $size=0, $limit=0, $link_images=null) {
		if($type == "slideshow") {
			if(!$size)
				$size = "m";
			if($link_images === null)
				$link_images = false;
		}
		else {
			$type = "gallery";
			if(!$size)
				$size = "75";
			if($link_images === null)
				$link_images = true;
		}
	
		if($limit < 1 || $limit > count($this->files))
			$limit = count($this->files);
	
		$html = '<ul class="'.$type.'">';
		
		$i = 0;
		
		while($i < $limit) {
			if(method_exists($this->files[$i], "thumb_html")) {
				$html .= "<li>";
				$html .= $this->files[$i]->thumb_html($size, $link_images);
				$html .= '</li>';
				$i++;
			}
		}
		
		$html .= '</ul>';
		
		return $html;
	}
}
