<?php

class Page extends Publishable {
	public $subpages = array();
	
	//get functions
	public function subpages() {
		return $this->subpages;
	}

	public function parents() {
		global $Lando;
		$path = trim_slashes($this->permalink);
		$slugs = explode("/", $path);
		array_pop($slugs);

		$path = "";
		$parents = array();

		foreach($slugs as $slug) {
			$path = "$path/$slug";
			$parents[] = $Lando->get_content("pages", $path);
		}

		return array_reverse($parents);
	}

	public function parent($n=1) {
		$parents = $this->parents();
		return isset($parents[$n]) ? $parents[$n] : false;
	}
}