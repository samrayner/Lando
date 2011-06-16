<?php include "inc/head.php" ?>
<body id="home">

<section id="primary">

	<h1>Drafts</h1>
	
	<? foreach($dp->getDrafts() as $i => $permalink): ?>

		<article>
			<h1><a href="<?= $dp->getSiteRoot().$permalink ?>"><?= $dp->getTitle($permalink) ?></a></h1>
			<footer>
				<p>Edited 
					<time pubdate datetime="<?= date('c', $dp->getModified($permalink)) ?>">
						<?= date('F jS \a\t g:ia', $dp->getModified($permalink)) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

</body>
<?php include "inc/foot.php" ?>