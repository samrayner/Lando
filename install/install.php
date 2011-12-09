<?php
$doc_root = dirname(dirname(dirname(__FILE__)));
include "$doc_root/app/core/loader.php";
 
$config = array(
	"admin_password" 		=> "",
	"site_title" 				=> "",
	"site_description" 	=> "",
	"site_root" 				=> preg_replace('~/admin/install/install\.php$~', "", current_url()),
	"host" 							=> "dropbox",
	"host_root"					=> "/Lando",
	"pretty_urls" 			=> 0,
	"cache_on_load" 		=> 1,
	"theme"							=> "default",
	"smartypants"				=> 1,
	"page_order" 				=> array()
);
 
$saved = 0;
 
if(sizeof($_POST) > 0) {
	foreach($config as $key => $val) {
		if(trim($_POST[$key]) !== "") {
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
	
	//$saved = @file_put_contents("$doc_root/app/config/config.php", "<?php\n\n".'$config = '.var_export($new_config, true).";");
	
	if($saved)
		setcookie("lando_password", $config["admin_password"], 0, "/", ".".$_SERVER['HTTP_HOST']);
}
 
//redirect to auth page for Dropbox
//redirect back
//store keys in app/config/<host_name>.php
//if install_content checked, install content
//notify install complete and suggest delete install folder
