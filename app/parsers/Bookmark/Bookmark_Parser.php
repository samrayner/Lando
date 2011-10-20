<?php

class Bookmark_Parser {
	public function parse($str) {
		//URL regex thanks to @imme_emosol
		$regex = '@(https?|ftp)://(-\.)?([^\s/?\.#-]+\.?)+(/[^\s<>"=]*)?@iS';
		
		if(preg_match_all($regex, $str, $matches))
			return end($matches[0]);
	
		return $str;
	}
}