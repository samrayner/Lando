<?php

class Content {
	protected $title;
	protected $modified;
	protected $path;
	protected $revision;
	
	protected function info() {
		//return instance vars as an array
	}
	
  function __toString()
  {
  	//return var_export of info
  }
	
	protected function cache() {
		//save to cache file
	}
}