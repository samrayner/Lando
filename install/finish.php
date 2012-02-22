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
	try {
		$oauth = array(
			"uid" => $_GET["uid"], //save UID in case we want it in a future build
			"token" => $Host->access_token()
		);
	}
	catch(DropLibException_OAuth $e) {
		system_error("Access Token Not Retrieved", "Could not retrieve an access token from the host.");
	}
	
	$token_saved = @file_put_contents("$doc_root/app/config/{$config["host"]}.php", "<?php\n\n".'$oauth = '.var_export($oauth, true).";");

	if(!$token_saved)
		system_error("oAuth Token Not Saved", "Could not save oAuth token. Please set permissions for <em>/app/config</em> and the files in it to <strong>755</strong> and try to install again.");
}
?>

<?php include "inc/head.php" ?>

<header>
	<h1>Install Lando</h1>
	
	<ol id="breadcrumbs">
		<li class="done">Site Info</li>
		<li class="done">Connect</li>
		<li class="current">Install</li>
	</ol>
</header>

<section id="install">
	<h1>1. Install Content</h1>
	<p class="subtitle">Add content to your Dropbox</p>

	<div>
		<?php if(!is_dir("$doc_root/install/content")): ?>
		No content found in install folder.
		<?php else: ?>
		<a id="install-button" class="button" href="#" data-icon="F">Install content in <?php echo $config["host_root"] ?></a>
		<p class="skip"><a href="#">I've installed my own content</a></p>
		<?php endif ?>
	</div>
</section>

<section id="cache" class="disabled">
	<h1>2. Cache Content</h1>
	<p class="subtitle">Prepare webpages from your cloud files</p>

	<div>
		<a id="recache-button" class="button" href="#" data-icon=")">Perform initial cache</a>
		<p class="skip"><a href="#">I'll cache later, thanks</a></p>
	</div>
</section>

<section id="cleanup" class="disabled">
	<h1>3. Clean-up Installation</h1>
	<p class="subtitle">Highly recommended for security reasons</p>

	<div>
		<a id="cleanup-button" class="button" href="#" data-icon="'">Delete install files</a> 
	</div>
</section>

<div id="last-panel" class="disabled">
	<p class="finished">Then you’re done!</p>
	<p>To check settings, visit the <a href="<?php echo $config["site_root"]."/admin/" ?>">admin panel</a> 
	or check out your new <a href="<?php echo $config["site_root"]."/" ?>">home page</a>!</p>
</div>

<?php include "inc/foot.php" ?>