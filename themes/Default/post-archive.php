<?php include "inc/head.php" ?>
<body id="post-archive">

<?php include "inc/header.php" ?>

<section id="primary">

	<?php 
		$year 	= url_segment(3);
		$month 	= url_segment(4);
		$day 		= url_segment(5);
		
		$date_str = "Archive";
		
		if($year) {
			$date_str = "from ".date("Y", mktime(0,0,0,1,1,$year));
			if($month) {
				$date_str = "from ".date("F, Y", mktime(0,0,0,$month,1,$year));
				if($day) {
					$date_str = "from ".date("F jS, Y", mktime(0,0,0,$month,$day,$year));
				}
			}
		}
	?>

	<h1>Posts <?php echo $date_str ?></h1>
	
	<?php $posts = posts(0, 0, $year, $month, $day);
		 foreach($posts as $i => $post): ?>
	
		<?php if(!$year && (
					!isset($posts[$i-1]) || //first post
					date('Y', $post->published) != date('Y', $posts[$i-1]->published) //first post of new year
		   )): ?>

			<h2><?php echo date('Y', $post->published) ?></h2>
		<?php endif ?>
	
		<?php if(!$month && (
					!isset($posts[$i-1]) || //first post
					date('n', $post->published) != date('n', $posts[$i-1]->published) //first post of new month
		   )): ?>

			<h2><?php echo date('F', $post->published) ?></h2>
		<?php endif ?>

		<article>
			<h1><a href="<?php echo $post->permalink ?>"><?php echo $post->title ?></a></h1>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?php echo date('c', $post->published) ?>">
						<?php echo date('F jS \a\t g:ia', $post->published) ?>
					</time>
				</p>
			</footer>
		</article>

	<?php endforeach ?>
	
</section>

<?php include "inc/footer.php" ?>

</body>
<?php include "inc/foot.php" ?>