<?php include "inc/head.php" ?>
<body id="draft-list">

<section id="primary">

	<h1>Drafts</h1>
	
	<? foreach(drafts() as $draft): ?>

		<article>
			<h1><a href="<?php echo $draft->permalink ?>"><?php echo $draft->title ?></a></h1>
			<footer>
				<p>Edited 
					<time pubdate datetime="<?php echo date('c', $draft->modified) ?>">
						<?php echo date('F jS \a\t g:ia', $draft->modified) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

</body>
<?php include "inc/foot.php" ?>