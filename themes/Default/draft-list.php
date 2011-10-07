<?php include "inc/head.php" ?>
<body id="draft-list">

<section id="primary">

	<h1>Drafts</h1>
	
	<?php foreach(drafts() as $draft): ?>

		<article>
			<h1><a href="<?php echo $draft->permalink() ?>"><?php echo $draft->title() ?></a></h1>
			<footer>
				<p>Edited 
					<time pubdate datetime="<?php echo $draft->modified('c')) ?>">
						<?php echo $draft->modified('F jS \a\t g:ia') ?>
					</time>
				</p>
			</footer>
		</article>

	<?php endforeach ?>
	
</section>

</body>
<?php include "inc/foot.php" ?>