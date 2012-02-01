<?php
$title = isset($_GET["title"]) ? stripslashes(urldecode($_GET["title"])) : "Error";
$message = isset($_GET["message"]) ? stripslashes(urldecode($_GET["message"])) : "An unkown error occured.";
?>

<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Error - <?php echo $site_title ?></title>
</head>
<body>
<div id="wrapper">

	<h1><?php echo $title ?></h1>
	<p><?php echo $message ?></p>

</div><!-- #wrapper -->
</body>
</html>