<?php include "inc/head.php" ?>
<body id="date-archive">

<?php include "inc/header.php" ?>

<section id="primary">

	<?php 
		$year 	= path_segment(3);
		$month 	= path_segment(4);
		$day 		= path_segment(5);
		
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
	
	<?php $posts = posts(0, 0, array(), $year, $month, $day);
		 		foreach($posts as $i => $post): ?>
	
		<?php if(!$year && (
					!isset($posts[$i-1]) || //first post
					$post->published('Y') != $posts[$i-1]->published('Y') //first post of new year
		   )): ?>

			<h2><?php echo date('Y', $post->published) ?></h2>
		<?php endif ?>
	
		<?php if(!$month && (
					!isset($posts[$i-1]) || //first post
					$post->published('n') != $posts[$i-1]->published('n') //first post of new month
		   )): ?>

			<h2><?php echo $post->published('F') ?></h2>
		<?php endif ?>

		<article>
			<h1><a href="<?php echo $post->permalink() ?>"><?php echo $post->title() ?></a></h1>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?php echo $post->published('c') ?>">
						<?php echo $post->published('F jS \a\t g:ia') ?>
					</time>
				</p>
				
				<?php if($post->metadata("tags")): ?>
					<h3>Tagged</h3>
					<ul>
					<?php foreach($post->metadata("tags") as $tag): ?>
						<li><a href="<?php echo $site_root ?>/posts/tagged/<?php echo urlencode($tag) ?>"><?php echo $tag ?></a></li>
					<?php endforeach ?>
					</ul>
				<?php endif ?>
			</footer>
		</article>

	<?php endforeach ?>
	
</section>

<?php include "inc/footer.php" ?>

</body>
<?php include "inc/foot.php" ?>