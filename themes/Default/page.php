<? include "inc/head.php" ?>

<body id="<?= $current->slug ?>">

<?php include "inc/header.php" ?>

<section id="primary">
	<article>
		<h1><?= $current->title ?></h1>
		<?= $current->content ?>
		<footer>
			<p>Posted <time pubdate datetime="<?= date('c', $current->published) ?>"><?= date('F jS \a\t g:ia', $current->published) ?></time></p>
		</footer>
	</article>
</section>

<?php include "inc/footer.php" ?>

</body>

<? include "inc/foot.php" ?>