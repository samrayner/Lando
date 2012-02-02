<?php 
header("HTTP/1.1 404 Not Found");

$url_source = "typed";

if(isset($_SERVER['HTTP_REFERER'])) {
	$url_source = "external";

	if(strpos($_SERVER['HTTP_REFERER'], $_SERVER['SERVER_NAME']) >= 0)
		$url_source = "internal";
}
?>

<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>
<div class="wrapper">

	<h1>404, Page Not Found</h1>

	<p>Sorry, but the page you were trying to view doesn't exist.</p>

	<p>
	<?php if($url_source == "typed"): ?>
	It looks like you might have mistyped the URL. Please check it and try again.

	<?php elseif($url_source == "external"): ?>
	It looks like you arrived here from another site or search engine, possibly via an out-of-date page. If you can, please let them know they have a broken link pointing here.

	<?php elseif($url_source == "internal"): ?>
	It looks like there's a broken link on the site. Sorry about that! Please let us know how you got here and we'll get it fixed.
	<?php endif ?>

	<br />Alternatively, you can try going back to <a href="<?php echo $site_root ?>">the homepage</a>.
	</p>
    
	<script>
		var GOOG_FIXURL_LANG = (navigator.language || '').slice(0,2),
		GOOG_FIXURL_SITE = location.host;
	</script>
	<script src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>

</div><!-- .wrapper -->
<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>