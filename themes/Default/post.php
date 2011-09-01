<?php include "inc/head.php" ?>
<body id="<?php echo $current->slug ?>">

<?php include "inc/header.php" ?>

<section id="primary">
	<article>
		<h1><?php echo $current->title ?></h1>
		<?php echo $current->content ?>
		<footer>
			<p>Posted 
				<time pubdate datetime="<?php echo date('c', $current->published) ?>">
					<?php echo date('F jS \a\t g:ia', $current->published) ?>
				</time>
			</p>
			
			<?php if($current->tags): ?>
				<h2>Tagged</h2>
				<ul>
				<?php foreach($current->tags as $tag): ?>
					<li><a href="<?php echo $site_root ?>/posts/tagged/<?php echo urlencode($tag) ?>"><?php echo $tag ?></a></li>
				<?php endforeach ?>
				</ul>
			<?php endif ?>
		</footer>
	</article>
</section>

<?php include "inc/footer.php" ?>

</body>
<?php include "inc/foot.php" ?>