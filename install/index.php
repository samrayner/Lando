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
		
		<ol id="breadcrumbs">
			<li>Connect</li>
			<li>Done!</li>
		</ol>
	</header>

	<section id="details">
		<h1>Website Details</h1>
		
		<div>
			<label for="site_title" class="field-label">Give Your Site a Title</label>
			<input type="text" id="site_title" name="site_title" value="Han's Space Journal" autofocus />
		</div>
		
		<div>
			<label for="site_description" class="field-label">Describe Your Site</label>
			<input type="text" id="site_description" name="site_description" value="Diary of a scruffy-looking Nerf-herder" />
		</div>
		
		<div>
			<label for="site_root" class="field-label">Root URL</label>
			<input type="text" id="site_root" name="site_root" placeholder="http://" value="<?php echo preg_replace('~/install/?(index\.php)?$~', "", current_url()) ?>" required />
		</div>
	</section>
	
	<section id="content">
		<h1>Cloud Content</h1>
	
		<div class="hidden">
			<label for="host">Select a Cloud Host</label>
			<?php 
				$selected = isset($config["host"]) ? $config["host"] : null;
				echo dropdown($hosts, $selected, array("id"=>"host", "name"=>"host"));
			?>
		</div>
	
		<div>
			<label for="host_root" class="field-label">Where content will live in your Dropbox</label>
			<input type="text" id="host_root" name="host_root" value="/Lando" />
		</div>
	</section>
	
	<section id="admin">
		<h1>Password for Admin &amp; Drafts</h1>
		
		<div>
			<label for="admin_password">Password</label>
			<input id="admin_password" name="admin_password" type="password" required />
		</div>
		
		<div>
			<label for="confirm_pass">Confirm Password</label>
			<input id="confirm_pass" name="confirm_pass" type="password" required />
		</div>
	</section>
	
	<div id="last-panel">
		<label for="next-connect">Next</label>
		<button id="next-connect" class="button" data-icon="C">Connect to Dropbox</button>
	</div>
</form>

<?php include "inc/foot.php" ?>