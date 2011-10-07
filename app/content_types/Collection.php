<?php

class Collection extends Content {
	public $files = array(); //of type File
	public $created;
	
	public function __toString() {
		return $this->list_html();
	}
	
	public function list_html() {
		$html = '<ul>';
		
		foreach($this->files() as $file)
			$html .= "<li>".$file->title()."</li>";
			
		$html .= '</ul>';
		
		return $html;
	}

	public function image_list_html($type="gallery", $size=0, $link_images=null) {
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
	
		$html = '<div class="'.$type.'">';
		
		foreach($this->files() as $file) {
			if(method_exists($file, "thumb_html"))
				$html .= $file->thumb_html($size, $link_images);
		}
		
		$html .= '</div>';
		
		return $html;
	}
	
	//get functions
		
	public function files() {
		return $this->files;
	}
	
	public function created($format="U") {
		return date($format, $this->created);
	}
}
