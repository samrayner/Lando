<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<article>
	<div class="page-header">
		<h1><?php echo $Current->title() ?></h1>
	</div>

	<?php echo $Current->content() ?>

	<footer>
		<div class="pubdate">
			<h3>Posted</h3>
			<p>
				<time datetime="<?php echo $Current->published('c') ?>">
					<?php echo $Current->published('F jS Y') ?>
				</time>
		</p>
		</div>
		
		<?php if($Current->metadata("tags")): ?>
		<div class="tags">
			<h3>Tagged</h3>
			<ul>
			<?php foreach($Current->metadata("tags") as $tag): ?>
				<li><a href="<?php echo $site_root ?>/posts/tagged/<?php echo urlencode($tag) ?>"><?php echo $tag ?></a></li>
			<?php endforeach ?>
			</ul>
		</div>
		<?php endif ?>
	</footer>
</article>

<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>