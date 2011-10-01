<?php

$config = array(
	"site_title" => "Test Site",
	"site_root" => "http://lando.dev",
	"site_description" => "Just another DropPub site.",
	"host" => "dropbox",
	"host_root" => "/Public/Lando/Test Site",
	"theme" => "default",
	"smartypants" => true,
	"pretty_urls" => false,
	
	"page_order" => array (
		'home' => 
		array (
		  '_hidden' => true,
		),
		'about-me' => '',
		'contact' => 
		array (
		  'everyone' => 
'',
		  'just-the-ceo' => 
'',
		  'just-me' => 
		  array (
		    'by-email' => 
		    array (
		      '_hidden' => true,
		    ),
		    'by-pigeon' => 
'',
		    '_hidden' => true,
		  ),
		)
	)
);