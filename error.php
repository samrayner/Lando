<?php
$title = isset($_GET["title"]) ? stripslashes(urldecode($_GET["title"])) : "Error";
$message = isset($_GET["message"]) ? stripslashes(urldecode($_GET["message"])) : "An unkown error occured.";
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Lando Error</title>

  <link rel="stylesheet" href="admin/css/admin.css" />
</head>
<body>
<div id="wrapper">

	<header>
		<h1><?php echo $title ?></h1>

		<div id="buttons">
			<a href="./" class="button" data-icon="H">Home</a>
		</div>
	</header>

	<div id="system-error">
		<p class="notify failure" data-icon="!"><?php echo $message ?></p>
	</div>

</div><!-- #wrapper -->
</body>
</html>