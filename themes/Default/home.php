<?php include "inc/head.php" ?>
<body id="home">

<header>
	<h1><a href="<?= $site_root ?>"><?= $site_title ?></a></h1>
	<? //$dp->printPageNav() ?>
</header>

<section id="primary">

	<h1><?= $current->title ?></h1>
	<?= $current->content ?>

	<? foreach(posts(5) as $post): ?>

		<article>
			<h1><a href="<?= $post->permalink ?>"><?= $post->title ?></a></h1>
			<?= truncate_html($post->content, 200) ?>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?= date('c', $post->published) ?>">
						<?= date('F jS \a\t g:ia', $post->published) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

<footer>
	<?= snippet("footer.md") ?>
</footer>

</body>
<?php include "inc/foot.php" ?>