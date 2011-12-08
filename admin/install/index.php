<?php
$doc_root = dirname(dirname(dirname(__FILE__)));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;

//load hosts list
foreach(glob("$doc_root/app/cloud_hosts/*", GLOB_ONLYDIR) as $dir)
	$hosts[] = basename($dir);
?>

<?php include "../inc/head.php" ?>

<form action="install.php" method="post" id="install-form">
	<header>
		<h1>Install Lando</h1>
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
			<input type="text" id="site_root" name="site_root" placeholder="http://" value="<?php echo preg_replace('~/admin/install/?(index.php)?$~', "", current_url()) ?>" />
		</div>
	</section>
	
	<section id="content">
		<h1>Dropbox Settings</h1>
		
		<div class="hidden">
			<label for="host">Host</label>
			<?php 
				$selected = isset($config["host"]) ? $config["host"] : null;
				echo dropdown($hosts, $selected, array("id"=>"host", "name"=>"host"));
			?>
		</div>
		
		<div>
			<label for="host_root" class="field-label">Content Location</label>
			<input type="text" id="host_root" name="host_root" value="/Apps/Lando" />
		</div>
		
		<div>
			<label for="install_content">Install dummy content</label>
			<input id="install_content" name="install_content" type="checkbox" value="1" checked />
		</div>
	</section>
	
	<section id="admin">
		<h1>Admin/Drafts Password</h1>
		
		<div>
			<label for="admin_password">Password</label>
			<input id="admin_password" name="admin_password" type="password" />
		</div>
		
		<div>
			<label for="confirm_pass">Confirm Password</label>
			<input id="confirm_pass" name="confirm_pass" type="password" />
		</div>
	</section>
	
	<div id="submit">
		<button id="save-bottom" class="button">Connect to Dropbox</button>
	</div>
</form>

<?php include "../inc/foot.php" ?>