<?php

function str_to_slug($str, $sep="-") {
	$output = strtolower(trim($str));
	$output = preg_replace("/\s+/",$sep,$output);
	$output = str_replace("&","and",$output);
	$output = preg_replace("/[^-a-z\d_]/",'',$output);
	return preg_replace("/-+/",'-',$output);
}

function trim_slashes($str) {
	return preg_replace('~^/+|/+$~', "", $str);
}