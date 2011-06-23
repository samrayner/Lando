<?php include "inc/head.php" ?>
<body id="draft-list">

<section id="primary">

	<h1>Drafts</h1>
	
	<? foreach(drafts() as $draft): ?>

		<article>
			<h1><a href="<?= $draft->permalink ?>"><?= $draft->title ?></a></h1>
			<footer>
				<p>Edited 
					<time pubdate datetime="<?= date('c', $draft->modified) ?>">
						<?= date('F jS \a\t g:ia', $draft->modified) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

</body>
<?php include "inc/foot.php" ?>