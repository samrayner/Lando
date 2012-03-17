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
		<li class="done">Connect</li>
		<li class="current">Done!</li>
	</ol>
</header>

<section id="install">
	<h1>Add Example Content</h1>
	<p class="subtitle">Helps getting to grips with Lando</p>

	<div>
		<?php if(!is_dir("$doc_root/install/content")): ?>
		No content found in install folder.
		<?php else: ?>
		<a id="install-button" class="button icon-download-alt" href="#">Put content in <?php echo $config["host_root"] ?></a>
		<?php endif ?>
	</div>
</section>

<section id="cache">
	<h1>Cache Content</h1>
	<p class="subtitle">Prepare webpages from your cloud files</p>

	<div>
		<a id="recache-button" class="button icon-play" href="#">Perform initial cache</a>
	</div>
</section>

<section id="cleanup">
	<h1>Clean Up Installation</h1>
	<p class="subtitle">Highly recommended for security reasons</p>

	<div>
		<a id="cleanup-button" class="button icon-trash" href="#">Delete install files</a> 
	</div>
</section>

<div id="last-panel">
	<p class="finished">&hellip;and you're done!</p>

	<div id="homepage-button">
		<a class="big done button icon-home" href="<?php echo $config["site_root"]."/" ?>">Go to your new homepage</a>
	</div>

	<p>Or to check your settings, visit the <a href="<?php echo $config["site_root"]."/admin/" ?>">admin panel</a>.</p>
</div>

<?php include "inc/foot.php" ?>