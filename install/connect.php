<?php
$doc_root = dirname(dirname(__FILE__));
set_include_path(get_include_path().":".$doc_root);

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include $file;

//get themes list
foreach(glob("$doc_root/themes/*", GLOB_ONLYDIR) as $dir)
	$themes[] = basename($dir);

$default_theme = isset($themes[0]) ? $themes[0] : "Default";

$base_url = preg_replace('~/install/'.basename($_SERVER['PHP_SELF']).'$~', "", current_url());

$config = array(
	"admin_password" 		=> "",
	"site_title" 				=> "My Website",
	"site_description" 	=> "Powered by Lando",
	"site_root" 				=> $base_url,
	"host" 							=> "Dropbox",
	"host_root"					=> "/Lando",
	"pretty_urls" 			=> 0,
	"theme"							=> $default_theme,
	"smartypants"				=> 1,
	"page_order" 				=> array()
);
 
$saved = 0;

if(empty($_POST)) {
	header("Location: $base_url/install/");
	exit;
}

foreach($config as $key => $val) {
	if(isset($_POST[$key]) && trim($_POST[$key]) !== "") {
		switch($key) {
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

if(!is_dir("$doc_root/app/config")) {
	$config_folder = @mkdir("$doc_root/app/config");

	if(!$config_folder)
		system_error("Config Not Saved", "Could not create config folder. Please set permissions for <em>/app</em> to <strong>777</strong> and try to install again.");
}

$config_file = @file_put_contents("$doc_root/app/config/config.php", "<?php\n\n".'$config = '.var_export($config, true).";");

if(!$config_file)
	system_error("Config Not Saved", "Could not update config file. Please set permissions for <em>/app/config</em> to <strong>777</strong> and try to install again.");

setcookie("lando_password", $config["admin_password"], 0, "/", ".".$_SERVER['HTTP_HOST']);
	
include "$doc_root/app/cloud_hosts/Cloud_Host.php";
include "$doc_root/app/cloud_hosts/{$config["host"]}/{$config["host"]}.php";

$host_class = str_replace(" ", "_", ucwords($config["host"]));
$Host = new $host_class($config);

try {
	$oauth = array("token" => $Host->request_token());
}
catch(DropLibException_OAuth $e) {
	system_error("Request Token Not Retrieved", "Could not retrieve a request token from the host.");
}

$token_file = @file_put_contents("$doc_root/app/config/{$config["host"]}.php", "<?php\n\n".'$oauth = '.var_export($oauth, true).";");

if(!$token_file)
	system_error("oAuth Token Not Saved", "Could not save oAuth token. Please set permissions for <em>/app/config</em> to <strong>777</strong> and try to install again.");

header("Location: ".$Host->authorize_url("$base_url/install/finish.php"));