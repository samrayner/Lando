<?php

include_once "MarkdownExtra.php";

class Markdown_Extra_Parser extends Parser {
	private $Markdown;

	public function __construct() {
		$this->Markdown = new MarkdownExtra_Parser();
	}

	public function parse($str) {
		return $this->Markdown->transform($str);
	}
}