<?php include "inc/head.php" ?>
<body id="post">

<header>
	<h1><a href="<?= $dp->getSiteRoot() ?>"><?= $dp->getSiteTitle() ?></a></h1>	
</header>

<section id="primary">
	<article>
		<h1><?= $dp->getTitle() ?></h1>
		<?= $dp->getContent() ?>
		<footer>
			<p>Posted 
				<time pubdate datetime="<?= date('c', $dp->getPublished()) ?>">
					<?= date('F jS \a\t g:ia', $dp->getPublished()) ?>
				</time>
			</p>
		</footer>
	</article>
</section>

<footer>
	<?= $dp->getSnippet("footer") ?>
</footer>

</body>
<?php include "inc/foot.php" ?>