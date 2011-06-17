<?php

//load config file
include_once "app/config/config.php";
//load host config
include_once "app/config/{$config['host']}.php";

//load controller
include_once "app/core/Controller.php";

//load models
include_once "app/core/Model.php";
include_once "app/cloud_hosts/Cloud_Host.php";
include_once "app/cloud_hosts/{$config['host']}/{$config['host']}.php";

//load all helper functions
foreach(glob("app/helpers/*.php") as $file)
	include_once $file;

//load parser base class
include_once "app/parsers/Parser.php";

//autoload parser or content classes when called
function __autoload($class) {
	//if parser class
	$format = str_replace("_Parser", "", $class);
	$file = "app/parsers/$format/$class.php";
	
	if($format != $class and file_exists($file)) {
		include_once $file;
		return;
	}
	
	//if content class
	$file = "app/content_types/$class.php";
	if(file_exists($file))
		include_once $file;
}

//provide hook for controller
$lando = new Controller();

//remove config from global scope (now accessible via controller)
unset($config);