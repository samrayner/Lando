<?php
$doc_root = $_SERVER['DOCUMENT_ROOT'];
 
//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;
 
$fields = array(
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
				case "site_root":
					$config[$field] = trim_slashes(str_replace(basename(request_url()), "", request_url()));
					break;
				case "pretty_urls":
				case "smartypants":
					$config[$field] = 0;
					break;
				case "host_root":
					$config[$field] = "/";
					break;
				case "page_order":
					$config[$field] = "{}";
					break;
				default: 
					$config[$field] = "";
			}
		}
		//if set, sanitize
		else {
			switch($field) {
				case "host":
				case "theme":
					$config[$field] = strtolower($_POST[$field]);
					break;
				case "site_root":
					$config[$field] = trim_slashes($_POST[$field]);
					break;
				case "host_root":
					$config[$field] = "/".trim_slashes($_POST[$field]);
					break;
				case "page_order":
					$config[$field] = json_decode(str_replace('\"', '"', $_POST[$field]), true);
					break;
				default: 
					$config[$field] = $_POST[$field];
			}
		}
	}
	
	$saved = @file_put_contents("$doc_root/app/config/config.php", "<?php\n\n".'$config = '.var_export($config, true).";");
}
 
//redirect up to parent directory
$protocol = (isset($_SERVER["HTTPS"])) ? "https" : "http";
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
header("Location: $protocol://$host$uri/?saved=".(bool)$saved);