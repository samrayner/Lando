<?php

class Blog_Item extends Publishable {
	private function sibling($offset) {
		global $Lando;

		$root = strtolower(get_called_class())."s";
		$all = $Lando->get_content($root);

		$index = array_search($this, $all);

		if(!isset($all[$index+$offset]))
			return false;
		
		return $all[$index+$offset];
	}

	public function next() {
		return $this->sibling(-1);
	}

	public function previous() {
		return $this->sibling(+1);
	}
}