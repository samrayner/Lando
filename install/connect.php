<?php
$doc_root = dirname(dirname(__FILE__));
set_include_path(get_include_path().":".$doc_root);

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include $file;

//get themes list
foreach(glob("$doc_root/themes/*", GLOB_ONLYDIR) as $dir)
	$themes[] = strtolower(basename($dir));

$default_theme = isset($themes[0]) ? $themes[0] : "default";

$base_url = preg_replace('~/install/'.basename($_SERVER['PHP_SELF']).'$~', "", current_url());

$config = array(
	"admin_password" 		=> "",
	"site_title" 				=> "",
	"site_description" 	=> "",
	"site_root" 				=> $base_url,
	"host" 							=> "dropbox",
	"host_root"					=> "/Lando",
	"pretty_urls" 			=> 0,
	"theme"							=> $default_theme,
	"smartypants"				=> 1,
	"page_order" 				=> array()
);
 
$saved = 0;
 
if(empty($_POST))
	header("Location: $base_url/install/");

foreach($config as $key => $val) {
	if(isset($_POST[$key]) && trim($_POST[$key]) !== "") {
		switch($key) {
			case "host":
				$config[$key] = strtolower($_POST[$key]);
				break;
			case "site_root":
				$config[$key] = trim_slashes($_POST[$key]);
				break;
			case "host_root":
				$config[$key] = "/".trim_slashes($_POST[$key]);
				break;
			default: 
				$config[$key] = $_POST[$key];
		}
	}
}

$config_saved = @file_put_contents("$doc_root/app/config/config.php", "<?php\n\n".'$config = '.var_export($config, true).";");

if(!$config_saved)
	throw new Exception("Error saving config file.");

setcookie("lando_password", $config["admin_password"], 0, "/", ".".$_SERVER['HTTP_HOST']);
	
include "$doc_root/app/cloud_hosts/Cloud_Host.php";
include "$doc_root/app/cloud_hosts/{$config["host"]}/{$config["host"]}.php";

$host_class = str_replace(" ", "_", ucwords($config["host"]));
$Host = new $host_class($config);

$oauth = array("token" => $Host->request_token());

$token_saved = @file_put_contents("$doc_root/app/config/{$config["host"]}.php", "<?php\n\n".'$oauth = '.var_export($oauth, true).";");

header("Location: ".$Host->authorize_url("$base_url/install/finish.php"));