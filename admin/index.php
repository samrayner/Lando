<?php
$doc_root = dirname(dirname(__FILE__));
include "$doc_root/app/core/loader.php";

$config = $Lando->config;

if(!admin_cookie())
	header("Location: ".$config["site_root"]."/admin/login.php?redirect=admin");

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
			echo 'value="'.stripslashes($val).'"';
	}
}

function nav_widget($pages=null, $path=array()) {
	global $Lando;

	if(!$pages) { //first run-through
		$html = '<div id="page-list">'."\n";

		$pages = $Lando->get_all_fresh("pages");

		$pages[] = new Page(array(
			"slug" => "posts",
			"title" => "Blog",
			"permalink" => "/posts/"
		));

		$html .= nav_widget($pages);
		$html .= '</div>';
		return $html;
	}

	$page_order = $Lando->config["page_order"];
	
	$tabs = str_repeat("\t", sizeof($path)*2);

	$html = $tabs.'<ol class="sortable">'."\n";

	foreach($pages as $Page) {
		$path[] = $Page->slug;
		
		$html .= "$tabs\t".'<li id="page_'.$Page->slug.'">'."\n$tabs\t\t<div>\n$tabs\t\t\t";
		$html .= '<input id="'.$Page->slug.'_visibility" type="checkbox" ';
		
		$active = $page_order;
		
		//step through page ordering to current nest level
		foreach($path as $next_key) {
			if(isset($active[$next_key]))
				$active = $active[$next_key];
		}
		
		if(!isset($active["_hidden"]) || $active["_hidden"] == false)
			$html .= "checked ";
		
		$html .= '/>'."\n$tabs\t\t\t";
		$html .= '<label for="'.$Page->slug.'_visibility">'.$Page->title.'</label>'."\n$tabs\t\t";
		$html .= "</div>\n";

		$subpages = $Page->subpages();

		if(!empty($subpages))
			$html .= nav_widget($subpages, $path);

		array_pop($path);

		$html .= "$tabs\t</li>\n";
	}

	$html .= "$tabs</ol>\n";
	
	return $html;
}

?>

<?php include "inc/head.php" ?>

<form action="save.php" method="post" id="admin-form">
	<header>
		<h1>Lando Admin</h1>
		
		<div id="buttons">
			<a href="<?php echo $site_root ?>" class="button" data-icon="H">Home</a>
			<a href="login.php?logout=1" class="button" data-icon="(">Log out</a>
		</div>
	</header>

	<section id="details">
	
		<?php
		
		if(isset($_GET["saved"])) {
			if($_GET["saved"])
				echo '<p class="notify success" data-icon="2">Settings saved. <a href="'.$site_root.'">All done?</a></p>';
			else
				echo '<p class="notify failure" data-icon="!">Error saving. Please check permissions on <em>app/config</em> and its files are <strong>0777</strong> and try again.</p>';
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
			<input type="text" id="site_root" name="site_root" placeholder="http://" <?php set_field_state("site_root"); ?> required />
		</div>
		
		<div>
			<label for="pretty_urls">Remove index.php from URLs</label>
			<input id="pretty_urls" name="pretty_urls" type="checkbox" value="1" <?php set_field_state("pretty_urls", "checked"); ?> />
			<p id="htaccess" class="notify collapsed" data-icon="!">Have you <a href="#">updated your <em>.htaccess</em> file</a>?</p>
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
			<label for="host_root" class="field-label">Path to Content</label>
			<input type="text" id="host_root" name="host_root" <?php set_field_state("host_root"); ?> />
		</div>
		
		<div id="recache">
			<a id="recache-button" class="button" href="#" data-icon="1">Recreate content caches</a>
		</div>
	</section>
	
	<section id="themes">
		<h1>Theme Options</h1>
		<!-- <p class="subtitle">Download more themes from <a href="#">GitHub</a>.</p> -->
		
		<div>
			<label for="theme">Theme</label>
			<?php 
				$selected = isset($config["theme"]) ? $config["theme"] : null;
				echo dropdown($themes, $selected, array("id"=>"theme", "name"=>"theme")); 
			?>
		</div>
		
		<div>
			<label for="smartypants">Use nice punctuation (&ldquo;curly quotes&rdquo;)</label>
			<input id="smartypants" name="smartypants" type="checkbox" value="1" <?php set_field_state("smartypants", "checked"); ?> />
		</div>
	</section>

	<section id="page-nav">
		<h1>Page Navigation</h1>
		<p class="subtitle">Uncheck to hide in nav, drag to reorder</p>
		
		<input id="page_order" name="page_order" type="hidden" />
	
		<?php echo nav_widget(); ?>
	</section><!-- #page-nav -->
	
	<section id="password">
		<h1>Change Admin/Drafts Password</h1>
		
		<div>
			<label for="admin_password">New Password</label>
			<input id="admin_password" name="admin_password" type="password" />
		</div>
		
		<div>
			<label for="confirm_pass">Confirm Password</label>
			<input id="confirm_pass" name="confirm_pass" type="password" />
		</div>
	</section>
	
	<div id="last-panel">
		<button class="button" data-icon="3">Save Settings</button>
		<a id="cancel" href="<?php echo $site_root ?>">Cancel</a>
	</div>
</form>

<?php include "inc/foot.php" ?>