<?php
$doc_root = $_SERVER['DOCUMENT_ROOT'];

//load all helper functions
foreach(glob("$doc_root/app/helpers/*.php") as $file)
	include_once $file;
	
//load existing config
$config_file = "$doc_root/app/config/config.php";
if(include_exists($config_file))
	include_once $config_file;

if(isset($_GET["logout"]) && $_GET["logout"]) {
	$expire_time = time()-3600;
	setcookie("admin_password", "", $expire_time, "/", ".".$_SERVER['HTTP_HOST']);
	unset($_COOKIE['admin_password']);
}

if(isset($_POST["password"])) {
	if($_POST["password"] != $config["admin_password"])
		$error = "Incorrect password. Please try again.";
	else {
		if(isset($_POST["remember"]) && $_POST["remember"])
			$expire_time = time()+4320000;
		else
			$expire_time = 0;
			
		setcookie("admin_password", $_POST["password"], $expire_time, "/", ".".$_SERVER['HTTP_HOST']);
		
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
		<h1>Private Area</h1>
		<a href="<?php echo $config["site_root"] ?>" class="button">Home</a>
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
			<button class="button">View</button>
		</div>
		
	</div>
</form>

<?php include "inc/foot.php" ?>