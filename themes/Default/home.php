<?php include "inc/head.php" ?>
<body id="home">

<?php include "inc/header.php" ?>

<section id="primary">

	<h1><?php echo $current->title ?></h1>
	<?php echo $current->content ?>

	<? foreach(posts(5) as $post): ?>

		<article>
			<h1><a href="<?php echo $post->permalink ?>"><?php echo $post->title ?></a></h1>
			<?php echo truncate_html($post->content, 200) ?>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?php echo date('c', $post->published) ?>">
						<?php echo date('F jS \a\t g:ia', $post->published) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

<?php include "inc/footer.php" ?>

</body>
<?php include "inc/foot.php" ?>