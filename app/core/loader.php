<?php

//if no config file, redirect to install
//else load config file
include_once "app/config/config.php";

//load host config
include_once "app/config/{$config['host']}.php";

//load host classes
include_once "app/hosts/Lando_Host.php";
include_once "app/hosts/{$config['host']}/Host.php";

//load controller
include_once "app/core/Lando.php";

//provide hook for controller
$lando = new Lando($config);

//unset config global
unset($config);

//load all helper functions
foreach(glob("app/helpers/*.php") as $file)
	include_once $file;

//load parser base class
include_once "app/parsers/Lando_Parser.php";

//autoload parser or content classes when called
function __autoload($class) {
	//if parser class
	$format = str_replace("_Parser", "", $class);
	$file = "app/parsers/$format/Parser.php";
	
	if($format != $class and file_exists($file)) {
		include_once $file;
		return;
	}
	
	//if content class
	$file = "app/content/$class.php";
	if(file_exists($file))
		include_once $file;
}