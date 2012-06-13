<?php

class Page extends Publishable {
	public $subpages = array();
	
	//get functions
	public function subpages() {
		return $this->subpages;
	}
	
	public function subpage($n=1) {
		return isset($this->subpages[$n-1]) ? $this->subpages[$n-1] : false;
	}

	public function parents() {
		$slugs = explode("/", trim_slashes($this->permalink));
		$trunk_slug = array_shift($slugs);

		global $Lando;
		$Trunk = $Lando->get_content("pages", $trunk_slug);

		$search_root = array_search_recursive($this->path, $Trunk->subpages, "path");

		if(!$search_root)
			return array();

		array_pop($search_root);

		$parents = array($Trunk);

		foreach($search_root as $index) {
			$Parent = end($parents);

			if(is_numeric($index))
				$parents[] = $Parent->subpages[$index];
		}

		//don't include self as a parent
		array_pop($parents);

		return $parents;
	}

	public function parent($n=1) {
		$parents = array_reverse($this->parents());

		return isset($parents[$n-1]) ? $parents[$n-1] : false;
	}
	
	public function siblings() {
		$Parent = $this->parent();
	
		if(!$Parent) {
			global $Lando;
			return $Lando->get_content("pages");
		}
	
		return $Parent->subpages();
	}
	
	public function sibling($offset) {
		$all = $this->siblings();

		$index = array_search($this, $all);

		if(!isset($all[$index+$offset]))
			return false;
		
		return $all[$index+$offset];
	}
}