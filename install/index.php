<?php
$doc_root = dirname(dirname(__FILE__));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;

//load hosts list
foreach(glob("$doc_root/app/cloud_hosts/*", GLOB_ONLYDIR) as $dir)
	$hosts[] = basename($dir);
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	
	<meta name="viewport" content="initial-scale=1.0, width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	
	<title>Install Lando</title>

	<link rel="icon" href="" />
	<link rel="apple-touch-icon" href="" />

	<link rel="stylesheet" href="../admin/css/admin.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
	<script src="../admin/js/min/admin-min.js"></script>
	
</head>
<body>
<div id="wrapper">

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
			<input type="text" id="site_root" name="site_root" placeholder="http://" value="<?php echo preg_replace('~/install/?(index.php)?$~', "", current_url()) ?>" required />
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
			<input id="admin_password" name="admin_password" type="password" required />
		</div>
		
		<div>
			<label for="confirm_pass">Confirm Password</label>
			<input id="confirm_pass" name="confirm_pass" type="password" required />
		</div>
	</section>
	
	<div id="submit">
		<button id="save-bottom" class="button">Connect to Dropbox</button>
	</div>
</form>

</div><!-- #wrapper -->
</body>
</html>