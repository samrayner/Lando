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
		
		<nav>
			<ol>
				<li><a href="index.php">Step 1</a></li>
				<li><a href="content.php">Step 2</a></li>
				<li class="current">Step 3</li>
			</ol>
		</nav>
	</header>

	<section id="cache">
		<h1>Cache Content</h1>
	
		<div id="cache">
			<a id="cache-button" class="button" href="#">Create content caches</a>
		</div>
	</section>
	
	<div id="submit">
		<p>Then you're done!</p>
		
		<div id="self-destruct">
			<p>It's highly recommended you <a class="button" href="#">delete these install files</a> for security reasons.</p>
		</div>
		
		<p>Now you can visit the <a href="<?php echo preg_replace('~/install/cache\.php$~', "/admin/", current_url()) ?>">admin area</a> 
		or your new <a href="<?php echo preg_replace('~/install/?(index\.php)?$~', "", current_url()) ?>">home page</a>!</p>
	</div>
</form>

<?php include "inc/foot.php" ?>