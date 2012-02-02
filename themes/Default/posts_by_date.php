<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<?php 
	$year 	= path_segment(3);
	$month 	= path_segment(4);
	$day 		= path_segment(5);
	
	$date_str = "Archive";
	
	if($year) {
		$date_str = "from ".date("Y", mktime(0,0,0,1,1,$year));
		if($month) {
			$date_str = "from ".date("F Y", mktime(0,0,0,$month,1,$year));
			if($day) {
				$date_str = "from ".date("F jS, Y", mktime(0,0,0,$month,$day,$year));
			}
		}
	}
?>

<h1>Posts <?php echo $date_str ?></h1>

<?php
	$posts = posts(0, 0, array(), $year, $month, $day);
	$posts_by_date = array();

	foreach($posts as $post)
		$posts_by_date[$post->published('Y')][$post->published('m')][$post->published('d')][] = $post;
?>
	
<?php foreach($posts_by_date as $post_year => $posts_by_month): ?>

	<?php if(!$year): ?>
		<div class="year-block">
		<h2>
			<a href="<?php echo "$site_root/posts/from/$post_year/" ?>/" title="Year permalink"><?php echo $post_year ?></a>
		</h2>
	<?php endif ?>

	<?php foreach($posts_by_month as $post_month => $posts_by_day): ?>

		<?php if(!$month): ?>
			<div class="month-block">
			<h3>
				<a href="<?php echo "$site_root/posts/from/$post_year/$post_month" ?>/" title="Month permalink"><?php echo date("F", mktime(0, 0, 0, $post_month)) ?></a>
			</h3>
		<?php endif ?>

		<?php foreach($posts_by_day as $post_day => $posts): ?>

			<div class="day-block">

			<?php foreach($posts as $post): ?>

				<article>
					<h1><a href="<?php echo $post->permalink() ?>"><?php echo $post->title() ?></a></h1>
					<footer>
						<div class="pubdate">
							<h3>Posted</h3>
							<p>
								<time datetime="<?php echo $post->published('c') ?>">
									<?php echo $post->published('l jS \a\t g:ia') ?>
								</time>
						</p>
						</div>
						
						<?php if($post->metadata("tags")): ?>
						<div class="tags">
							<h3>Tagged</h3>
							<ul>
							<?php foreach($post->metadata("tags") as $tag): ?>
								<li><a href="<?php echo $site_root ?>/posts/tagged/<?php echo urlencode($tag) ?>"><?php echo $tag ?></a></li>
							<?php endforeach ?>
							</ul>
						</div>
						<?php endif ?>
					</footer>
				</article>

			<?php endforeach; //posts ?>

			</div><!-- .day-block -->

		<?php endforeach; //days ?>

		<?php if(!$month): ?></div><!-- .month-block --><?php endif ?>

	<?php endforeach; //months ?>

	<?php if(!$year): ?></div><!-- .year-block --><?php endif ?>

<?php endforeach; //years ?>

<?php if(empty($posts_by_date)): ?>
	<p>No posts found <?php echo $date_str ?>.</p>
<?php endif ?>
	
<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>