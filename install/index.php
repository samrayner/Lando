<?php
$doc_root = dirname(dirname(__FILE__));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;

//load hosts list
foreach(glob("$doc_root/app/cloud_hosts/*", GLOB_ONLYDIR) as $dir)
	$hosts[] = basename($dir);
?>

<?php include "inc/head.php" ?>

<form action="connect.php" method="post" id="details-form">
	<header>
		<h1>Install Lando</h1>
		
		<nav>
			<ol>
				<li class="current">Step 1: Site Details</li>
				<li>Step 2</li>
				<li>Step 3</li>
			</ol>
		</nav>
	</header>

	<section id="details">
		<h1>Site Details</h1>
		
		<div>
			<label for="site_title" class="field-label">Title</label>
			<input type="text" id="site_title" name="site_title" value="Bespin Daily" />
		</div>
		
		<div>
			<label for="site_description" class="field-label">Description</label>
			<input type="text" id="site_description" name="site_description" value="All the latest from the cloud city." />
		</div>
		
		<div>
			<label for="site_root" class="field-label">Root URL</label>
			<input type="text" id="site_root" name="site_root" placeholder="http://" value="<?php echo preg_replace('~/install/?(index\.php)?$~', "", current_url()) ?>" required />
		</div>
	</section>
	
	<section id="admin">
		<h1>Admin/Drafts Password</h1>
		
		<div>
			<label for="admin_password">Password</label>
			<input id="admin_password" name="admin_password" type="password" required />
		</div>
		
		<div>
			<label for="confirm_pass">Confirm Password</label>
			<input id="confirm_pass" name="confirm_pass" type="password" required />
		</div>
	</section>
	
	<section id="host" class="hidden">
		<h1>Content Host</h1>
		
		<div>
			<label for="host">Select a Cloud Host</label>
			<?php 
				$selected = isset($config["host"]) ? $config["host"] : null;
				echo dropdown($hosts, $selected, array("id"=>"host", "name"=>"host"));
			?>
		</div>
	</section>
	
	<div id="submit">
		<label for="next-connect">Next Step</label>
		<button id="next-connect" class="button">Connect to Dropbox</button>
	</div>
</form>

<?php include "inc/foot.php" ?>