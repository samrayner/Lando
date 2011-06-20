<?php

include_once "Textile.php";

class Textile_Parser extends Parser {
	private $Textile;

	public function __construct() {
		$this->Textile = new Textile();
	}

	public function parse($str) {
		return $this->Textile->TextileThis($str);
	}
}