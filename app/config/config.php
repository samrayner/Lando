<?php

$config = array(
	"site_title" => "Test Site",
	"site_root" => "http://lando.dev",
	"site_description" => "Just another DropPub site.",
	"host" => "dropbox",
	"host_root" => "/Public/Lando/Test Site",
	"admin_pass" => "",
	"theme" => "default",
	"smartypants" => true,
	"pretty_urls" => false,
	
	"page_order" => array(
		"home" => '',
		"contact" => 	array(
			"everyone" => '',
			"just-me" => array(
				"by-email" => ''
			)
	 	),
	 	"about-me" => '',
	 	"should-auto-strip-and-format" => '',
	 	"this-used-to-have-punctuations" => ''
	)
);