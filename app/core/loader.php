<?php

//load all helper functions
foreach(glob("app/helpers/*.php") as $file)
	include_once $file;

//load config file
include_once "app/config/config.php";

//load controller class
include_once "app/core/Controller.php";

//load cache class
include_once "app/core/Cache.php";

//load model classes
include_once "app/core/Model.php";
include_once "app/cloud_hosts/Cloud_Host.php";
include_once "app/cloud_hosts/{$config['host']}/{$config['host']}.php";

//load SmartyPants formatter class
include_once "app/parsers/SmartyPants.php";

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

//create list of parsers with supported formats
$config["parsers"] = array();
foreach(glob("app/parsers/*", GLOB_ONLYDIR) as $dir) {
	$format = basename($dir);
	$ext_list = @file_get_contents($dir."/extensions.txt");
	if($ext_list)
		$config["parsers"][$format] = explode("\n", $ext_list);
}

//provide hook for controller
$Lando = new Controller();

//extract global theme variables
extract($Lando->theme_vars);

//remove config from global scope
unset($config);