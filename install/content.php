<?php
$doc_root = dirname(dirname(__FILE__));
include "inc/head.php";
?>

<form action="cache.php" method="post" id="connect-form">
	<header>
		<h1>Install Lando</h1>
		
		<nav>
			<ol>
				<li><a href="index.php">Step 1</a></li>
				<li class="current">Step 2</li>
				<li>Step 3</li>
			</ol>
		</nav>
	</header>
	
	<section id="content">
		<h1>Dropbox Settings</h1>
	
		<div>
			<label for="host_root" class="field-label">Content Location</label>
			<input type="text" id="host_root" name="host_root" value="/Apps/Lando" />
		</div>
		
		<div id="connect">
			<?php if(is_dir("$doc_root/install/example_content")): ?>
			<button id="connect-button" class="button">Install example content</button> or just 
			<?php endif ?>
			<button id="connect-button" class="button">Create the content folders</button>
		</div>
	</section>
	
	<div id="submit">
		<label for="cache-step">Next</label>
		<button id="cache-step" class="button">Cache content</button>
	</div>
</form>

<?php include "inc/foot.php" ?>