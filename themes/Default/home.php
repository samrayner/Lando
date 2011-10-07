<?php include "inc/head.php" ?>
<body id="home">

<?php include "inc/header.php" ?>

<section id="primary">

	<h1><?php echo $current->title() ?></h1>
	<?php echo $current->content() ?>

	<?php foreach(posts(5) as $post): ?>

		<article>
			<h1><a href="<?php echo $post->permalink() ?>"><?php echo $post->title() ?></a></h1>
			<?php echo truncate_html($post->content(), 200) ?>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?php echo $post->published('c') ?>">
						<?php echo $post->published('F jS \a\t g:ia') ?>
					</time>
				</p>
				
				<?php if($post->tags()): ?>
					<h3>Tagged</h3>
					<ul>
					<?php foreach($post->tags() as $tag): ?>
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