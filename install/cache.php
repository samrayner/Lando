<?php
$doc_root = dirname(dirname(__FILE__));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;
?>

<?php include "inc/head.php" ?>

<form action="connect.php" method="post" id="details-form">
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
			<a class="button" href="#" data-icon="1">Perform initial cache</a>
		</div>
	</section>
	
	<section id="delete">
		<h1>Install Clean-up</h1>
		<p class="subtitle">Highly recommended for security reasons</p>
	
		<div id="cache">
			<a class="button" href="#" data-icon="#">Delete install files</a> 
		</div>
	</section>
	
	<div id="last-panel">
		<p class="finished">Then you’re done!</p>
		<p>To check settings, visit the <a href="<?php echo preg_replace('~/install/cache\.php$~', "/admin/", current_url()) ?>">admin area</a> 
		or check out your new <a href="<?php echo preg_replace('~/install/?(index\.php)?$~', "", current_url()) ?>">home page</a>!</p>
	</div>
</form>

<?php include "inc/foot.php" ?>