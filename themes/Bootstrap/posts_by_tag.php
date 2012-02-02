<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<?php 
	$csv = explode(",", path_segment(3));
	$tags = array();
	foreach($csv as $i => $tag) {
		$tag = trim($tag);
		if($tag !== "")
			$tags[$i] = urldecode($tag);
	}
?>

<div class="page-header">
	<h1>Posts tagged <?php echo '"'.implode(", ", $tags).'"' ?></h1>
</div>

<?php $posts = posts(0, 0, array("tags"=>$tags));
	 		foreach($posts as $post): ?>

	<article>
		<h1><a href="<?php echo $post->permalink() ?>"><?php echo $post->title() ?></a></h1>
		<?php echo truncate_html($post->content(), 200) ?>
		<footer>
			<div class="pubdate">
				<h3>Posted</h3>
				<p>
					<time datetime="<?php echo $post->published('c') ?>">
						<?php echo $post->published('F jS Y') ?>
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

<?php endforeach ?>

<?php if(empty($posts)): ?>
	<p>No posts found tagged <?php echo '"'.implode(", ", $tags).'"' ?>.</p>
<?php endif ?>

<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>