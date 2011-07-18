<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="description" content="<?php echo $site_description ?>" />
	
	<?php if(url_segment(1) == "drafts") echo '<meta name="robots" content="noindex, nofollow" />' ?>
	
	<title><?php echo $site_title ?></title>
	
	<!-- Default styles -->
	<link rel="stylesheet" href="<?php echo $theme_dir ?>/css/screen.css" media="screen" />
	<!-- PrettyPhoto styles -->
	<link rel="stylesheet" href="<?php echo $theme_dir ?>/css/prettyPhoto.css" media="screen" />
	<!-- Flux styles -->
	<link rel="stylesheet" href="<?php echo $theme_dir ?>/css/flux.css" media="screen" />
	
	<!--[if lte IE 8]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
	
  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if necessary -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1/jquery.js"></script>
	<script>window.jQuery || document.write('<script src="<?php echo $theme_dir ?>/js/jquery-1.6.2.min.js">\x3C/script>')</script>
	
	<!-- PrettyPhoto for galleries: http://www.no-margin-for-errors.com/projects/prettyphoto-jquery-lightbox-clone/ -->
	<script src="<?php echo $theme_dir ?>/js/prettyPhoto/jquery.prettyPhoto.js"></script>
	<!-- Flux slider for slideshows: http://www.joelambert.co.uk/flux/ -->
	<script src="<?php echo $theme_dir ?>/js/flux/flux.min.js"></script>
	
	<script>
		$(function(){
			window.f = new flux.slider('.slideshow', {
				/*There are loads of settings you can adjust here to personalise
					your slideshows. Consult the Flux documentation at 
					http://goo.gl/Tj0b5 */
			});
			
			//link images in the same gallery together for navigation
			$(".gallery a").each(function(index) {
				var offset = $(this).parent().offset();
				var id = offset.top + offset.left;
		    $(this).attr("rel", "prettyPhoto[" + id + "]")
		  });
			
			$("a[rel^='prettyPhoto']").prettyPhoto({
				/*There are loads of settings you can adjust here to personalise
					your galleries. Consult the PrettyPhoto documentation at 
					http://goo.gl/1mk4h */
			});
		});
	</script>
	
</head>