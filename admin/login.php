<?php
$doc_root = dirname(dirname(__FILE__));

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;
	
//load existing config
$config_file = "$doc_root/app/config/config.php";
if(include_exists($config_file))
	include_once $config_file;

if(isset($_GET["logout"]) && $_GET["logout"]) {
	$expire_time = time()-3600;
	setcookie("lando_password", "", $expire_time, "/", ".".$_SERVER['HTTP_HOST']);
	unset($_COOKIE['lando_password']);
	header("Location: ".$config["site_root"]);
}

if(isset($_POST["password"])) {
	if($_POST["password"] != $config["admin_password"])
		$error = "Incorrect password. Please try again.";
	else {
		if(isset($_POST["remember"]) && $_POST["remember"])
			$expire_time = time()+4320000;
		else
			$expire_time = 0;
			
		setcookie("lando_password", $_POST["password"], $expire_time, "/", ".".$_SERVER['HTTP_HOST']);
		
		$redirect = trim_slashes($config["site_root"]);
		
		if(!$config["pretty_urls"])
			$redirect .= "/index.php";

		if(isset($_GET["redirect"]))
			$redirect .= "/".trim_slashes($_GET["redirect"]);

		header("Location: $redirect");
	}
}

$self = $_SERVER['PHP_SELF'];
if(isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'])
	$self .= "?".$_SERVER['QUERY_STRING'];
?>

<?php include "inc/head.php" ?>

<form action="<?php echo $self ?>" method="post" id="login-form">
	<header>
		<h1 data-icon="(">Login Required</h1>
		<a href="<?php echo $config["site_root"] ?>" class="button" data-icon="H">Home</a>
	</header>

	<div id="login">
	
		<?php
		if(isset($error))
				echo '<p class="failure message">'.$error.'</p>';
		?>
		
		<div id="pass-field">
			<label for="password" class="field-label">Password</label>
			<input type="password" id="password" name="password" autofocus />
		</div>
		
		<div>
			<input type="checkbox" id="remember" name="remember" />
			<label for="remember">Remember me</label>
		</div>
		
		<div id="login-button">
			<button class="button" data-icon="K">Log In</button>
		</div>
		
	</div>
</form>

<?php include "inc/foot.php" ?>