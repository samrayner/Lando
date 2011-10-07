<?php include "inc/head.php" ?>

<body id="<?php echo $current->slug() ?>">

<?php include "inc/header.php" ?>

<section id="primary">
	<article>
		<h1><?php echo $current->title() ?></h1>
		<?php echo $current->content() ?>
		<footer>
			<p>Updated <time pubdate datetime="<?php echo $current->modified('c') ?>"><?php echo $current->modified('F jS \a\t g:ia') ?></time></p>
		</footer>
	</article>
</section>

<?php include "inc/footer.php" ?>

</body>

<?php include "inc/foot.php" ?>