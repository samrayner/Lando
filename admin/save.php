<?php
$doc_root = dirname(dirname(__FILE__));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;
	
//load existing config
$config_file = "$doc_root/app/config/config.php";
if(include_exists($config_file))
	include_once $config_file;
	
include "inc/auth.php";
 
$fields = array(
	"admin_password",
	"site_title",
	"site_description",
	"site_root",
	"pretty_urls",
	"host",
	"host_root",
	"theme",
	"smartypants",
	"page_order"
);
 
$saved = 0;
 
if(sizeof($_POST) > 0) {
	foreach($fields as $field) {
		//not set or blank
		if(!isset($_POST[$field]) || !$_POST[$field]) {
			switch($field) {
				case "admin_password":
					$new_config[$field] = $config[$field];
					break;
				case "site_root":
					$new_config[$field] = trim_slashes(url_segment(0));
					break;
				case "pretty_urls":
				case "smartypants":
					$new_config[$field] = 0;
					break;
				case "host_root":
					$new_config[$field] = "/";
					break;
				case "page_order":
					$new_config[$field] = "{}";
					break;
				default: 
					$new_config[$field] = "";
			}
		}
		//if set, sanitize
		else {
			switch($field) {
				case "host":
				case "theme":
					$new_config[$field] = strtolower($_POST[$field]);
					break;
				case "site_root":
					$new_config[$field] = trim_slashes($_POST[$field]);
					break;
				case "host_root":
					$new_config[$field] = "/".trim_slashes($_POST[$field]);
					break;
				case "page_order":
					$new_config[$field] = json_decode(str_replace('\"', '"', $_POST[$field]), true);
					break;
				default: 
					$new_config[$field] = $_POST[$field];
			}
		}
	}
	
	$saved = @file_put_contents("$doc_root/app/config/config.php", "<?php\n\n".'$config = '.var_export($new_config, true).";");
	
	if($saved && $new_config["admin_password"] != $config["admin_password"])
		setcookie("lando_password", $new_config["admin_password"], 0, "/", ".".$_SERVER['HTTP_HOST']);
}
 
//redirect up to parent directory
$protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: $protocol://$host$uri/?saved=".(bool)$saved);