<?php
$doc_root = $_SERVER['DOCUMENT_ROOT'];
include "$doc_root/app/core/loader.php";

$config = $Lando->config;

foreach(glob("$doc_root/app/cloud_hosts/*", GLOB_ONLYDIR) as $dir)
	$hosts[] = basename($dir);

foreach(glob("$doc_root/themes/*", GLOB_ONLYDIR) as $dir)
	$themes[] = basename($dir);

function set_field_state($key, $attr=null) {
	global $config;
	$val = $config[$key];
	
	if(isset($val) && $val) {
		if($attr)
			echo $attr;
		else
			echo 'value="'.$val.'"';
	}
}

function nav_widget($pages=null, $path=array()) {
	if(!$pages) { //first run-through
		$html = '<div id="page-list">'."\n";
		$html .= nav_widget(pages());
		$html .= '</div>';
		return $html;
	}

	global $config;
	$page_order = $config["page_order"];
	
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = $tabs.'<ol class="sortable">'."\n";

	foreach($pages as $page) {
		$path[] = $page->slug;
		
		$html .= "$tabs\t".'<li id="'.$page->slug.'">'."\n$tabs\t\t<div>\n$tabs\t\t\t";
		$html .= '<input id="'.$page->slug.'_visibility" type="checkbox" ';
		
		$current = $page_order;
		
		//step through page ordering to current nest level
		foreach($path as $next_key) {
			if(isset($current[$next_key]))
				$current = $current[$next_key];
		}
		
		if(!isset($current["_hidden"]) || $current["_hidden"] == false)
			$html .= "checked ";
		
		$html .= '/>'."\n$tabs\t\t\t";
		$html .= '<label for="'.$page->slug.'_visibility">'.$page->title.'</label>'."\n$tabs\t\t";
		$html .= "</div>\n";

		if(!empty($page->subpages))
			$html .= nav_widget($page->subpages, $path);

		array_pop($path);

		$html .= "$tabs\t</li>\n";
	}

	$html .= "$tabs</ol>\n";
	
	return $html;
}

?>

<!doctype html>
<html lang="en-gb">

<head>
	<meta charset="utf-8" />
	
	<meta name="viewport" content="initial-scale=1.0, width=device-width, user-scalable=no">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />
	
	<title>Lando Admin</title>

	<link rel="icon" href="" />
	<link rel="apple-touch-icon" href="" />

	<link rel="stylesheet" href="css/master.css" />

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
	<script src="js/jquery.ui.touch-punch.min.js"></script>
	<script src="js/admin.js"></script>
	
</head>
<body>
<div id="wrapper">

<form action="save.php" method="post">
	<header>
		<h1>Lando Settings</h1>
		<button id="save-top" class="button">Save</button>
	</header>

	<section>
	
		<?php
		
		if(isset($_GET["saved"])) {
			if($_GET["saved"])
				echo '<p class="success message">Settings saved</p>';
			else
				echo '<p class="failure message">Error saving. Please check permissions on <em>app/config/config.php</em> are <strong>777</strong> and try again.</p>';
		}
		
		?>
	
		<h1>Site Details</h1>
		
		<div>
			<label for="site_title" class="field-label">Title</label>
			<input type="text" id="site_title" name="site_title" <?php set_field_state("site_title"); ?> />
		</div>
		
		<div>
			<label for="site_description" class="field-label">Description</label>
			<input type="text" id="site_description" name="site_description" <?php set_field_state("site_description"); ?> />
		</div>
		
		<div>
			<label for="site_root" class="field-label">Root URL</label>
			<input type="text" id="site_root" name="site_root" placeholder="http://" <?php set_field_state("site_root"); ?> />
		</div>
		
		<div>
			<label for="pretty_urls">Remove index.php from URLs</label>
			<input id="pretty_urls" name="pretty_urls" type="checkbox" value="1" <?php set_field_state("pretty_urls", "checked"); ?> />
		</div>
	</section>
	
	<section>
		<h1>Content Source</h1>
		
		<div>
			<label for="host">Host</label>
			<?php 
				$selected = isset($config["host"]) ? $config["host"] : null;
				echo dropdown($hosts, $selected, array("id"=>"host", "name"=>"host"));
			?>
		</div>
		
		<div>
			<label for="host_root" class="field-label">Path to Content</label>
			<input type="text" id="host_root" name="host_root" <?php set_field_state("host_root"); ?> />
		</div>
	</section>
	
	<section>
		<h1>Theme Options</h1>
		<!-- <p>Download more themes from <a href="#">GitHub</a>.</p> -->
		
		<div>
			<label for="theme">Theme</label>
			<?php 
				$selected = isset($config["theme"]) ? $config["theme"] : null;
				echo dropdown($themes, $selected, array("id"=>"theme", "name"=>"theme")); 
			?>
		</div>
		
		<div>
			<label for="smartypants">Use nice punctuation (e.g. &ldquo;curly quotes&rdquo;)</label>
			<input id="smartypants" name="smartypants" type="checkbox" value="1" <?php set_field_state("smartypants", "checked"); ?> />
		</div>
	</section>

	<section id="page-nav">
		<h1>Page Navigation</h1>
		<p>Uncheck to hide from nav, drag to reorder</p>
		
		<input id="page_order" name="page_order" type="hidden" />
	
		<?php echo nav_widget(); ?>
	</section><!-- #page-nav -->
	
	<div id="submit">
		<button id="save-bottom" class="button">Save Settings</button>
		<a id="cancel" href="<?php echo $site_root ?>">Cancel</a>
	</div>
</form>

</div><!-- #wrapper -->
</body>
</html>