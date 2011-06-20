<?php

class Image extends File {
	public $width;
	public $height;
	public $thumbs;
	
	private $thumb_sizes = array(
		"icon" 	=> array("max_width" => 16, 	"max_height" => 16, 	"id" => "16x16"), 
		"64" 		=> array("max_width" => 64, 	"max_height" => 64, 	"id" => "64x64"), 
		"75"		=> array("max_width" => 75, 	"max_height" => 75, 	"id" => "75x75_fit_one"),
		"150" 	=> array("max_width" => 150, 	"max_height" => 150, 	"id" => "150x150_fit_one"),
		"s" 		=> array("max_width" => 320, 	"max_height" => 240, 	"id" => "320x240_bestfit"),
		"m" 		=> array("max_width" => 480, 	"max_height" => 320, 	"id" => "480x320_bestfit"), 
		"l" 		=> array("max_width" => 640, 	"max_height" => 480, 	"id" => "640x480_bestfit"),
		"xl" 		=> array("max_width" => 960, 	"max_height" => 640, 	"id" => "960x640_bestfit"),
		"xxl" 	=> array("max_width" => 1024, "max_height" => 768, 	"id" => "1024x768_bestfit")
	);
}