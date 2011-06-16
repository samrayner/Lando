<?php include "inc/head.php" ?>
<body id="home">

<header>
	<h1><a href="<?= $dp->getSiteRoot() ?>"><?= $dp->getSiteTitle() ?></a></h1>
	<? $dp->printPageNav() ?>
</header>

<section id="primary">

	<h1><?= $dp->getTitle("home"); ?></h1>
	<?= $dp->getContent("home"); ?>

	<? foreach($dp->getPosts(5) as $permalink): ?>

		<article>
			<h1><a href="<?= $permalink ?>"><?= $dp->getTitle($permalink) ?></a></h1>
			<?= $dp->truncate($dp->getContent($permalink), 200); ?>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?= date('c', $dp->getPublished($permalink)) ?>">
						<?= date('F jS \a\t g:ia', $dp->getPublished($permalink)) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

<footer>
	<?= $dp->getSnippet("footer") ?>
</footer>

</body>
<?php include "inc/foot.php" ?>