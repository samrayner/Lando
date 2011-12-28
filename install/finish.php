<?php
$doc_root = dirname(dirname(__FILE__));
set_include_path(get_include_path().":".$doc_root);

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include $file;

include "$doc_root/app/config/config.php";
include "$doc_root/app/cloud_hosts/Cloud_Host.php";
include "$doc_root/app/cloud_hosts/{$config["host"]}/{$config["host"]}.php";

$base_url = preg_replace('~/install/?(index\.php)?$~', "", current_url());

$host_class = str_replace(" ", "_", ucwords($config["host"]));
$Host = new $host_class($config);

//only save access token if redirected from Dropbox oAuth page
if(isset($_GET["uid"])) {
	//save UID in case we want it in a future build
	$oauth = array(
		"uid" => $_GET["uid"], 
		"token" => $Host->access_token()
	);
	
	$token_saved = @file_put_contents("$doc_root/app/config/{$config["host"]}.php", "<?php\n\n".'$oauth = '.var_export($oauth, true).";");
}
?>

<?php include "inc/head.php" ?>

<header>
	<h1>Install Lando</h1>
	
	<ol id="breadcrumbs">
		<li class="done">Site Info</li>
		<li class="done">Connect</li>
		<li class="current">Launch!</li>
	</ol>
</header>

<section id="cache">
	<h1>Cache Content</h1>
	<p class="subtitle">Prepare webpages from your cloud files</p>

	<div id="cache">
		<a id="recache-button" class="button" href="#" data-icon="9">Perform initial cache</a>
	</div>
</section>

<section id="delete">
	<h1>Installation Clean-up</h1>
	<p class="subtitle">Highly recommended for security reasons</p>

	<div id="cache">
		<a class="button" id="cleanup-button" href="#" data-icon="#">Delete install files</a> 
	</div>
</section>

<div id="last-panel">
	<p class="finished">Then you’re done!</p>
	<p>To check settings, visit the <a href="<?php echo $config["site_root"]."/admin/" ?>">admin area</a> 
	or check out your new <a href="<?php echo $config["site_root"]."/" ?>">home page</a>!</p>
</div>

<?php include "inc/foot.php" ?>