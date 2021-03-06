<?php

class File extends Content {
	public $format;
	public $extension;
	public $order;
	public $bytes;
	public $url;
	public $mime_type;
	
	public function __toString() {
		return $this->content();
	}
	
	public function download_link() {
		return '<a href="'.$this->url.'">'.$this->title.'</a>';
	}
	
	protected function to_html($content) {
		if($content && $this->format) {
			$parser_class = $this->format."_Parser";
			if(class_exists($parser_class)) {
				$Parser = new $parser_class();
				$content = $Parser->parse($content);
			}
		}
		
		return $content;
	}
	
	//get functions
	
	public function content() {
		return $this->to_html($this->raw_content);
	}
	
	public function format() {
		return $this->format;
	}
	
	public function extension() {
		return $this->extension;
	}

	public function order() {
		return $this->order;
	}

	public function bytes() {
		return $this->bytes;
	}

	public function size($unit="kb") {
		return format_bytes($this->bytes, $unit);
	}

	public function url() {
		return $this->url;
	}

	public function mime_type() {
		return $this->mime_type;
	}
}