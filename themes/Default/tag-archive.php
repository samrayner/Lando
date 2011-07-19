<?php include "inc/head.php" ?>
<body id="tag-archive">

<?php include "inc/header.php" ?>

<section id="primary">

	<?php 
		$csv = explode(",", url_segment(3));
		$tags = array();
		foreach($csv as $i => $tag) {
			$tag = trim($tag);
			if($tag !== "")
				$tags[$i] = urldecode($tag);
		}
	?>

	<h1>Posts tagged <?php echo implode(", ", $tags) ?></h1>
	
	<?php $posts = posts(0, 0, $tags);
		 		foreach($posts as $i => $post): ?>

		<article>
			<h1><a href="<?php echo $post->permalink ?>"><?php echo $post->title ?></a></h1>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?php echo date('c', $post->published) ?>">
						<?php echo date('F jS \a\t g:ia', $post->published) ?>
					</time>
				</p>
				
				<?php if($post->tags): ?>
					<h3>Tagged</h3>
					<ul>
					<?php foreach($post->tags as $tag): ?>
						<li><?php echo $tag ?></li>
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