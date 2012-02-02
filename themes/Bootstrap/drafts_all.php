<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<div class="page-header">
	<h1>Drafts</h1>
</div>

<?php foreach(drafts() as $draft): ?>

	<article>
		<h1><a href="<?php echo $draft->permalink() ?>"><?php echo $draft->title() ?></a></h1>
		<footer>
			<div class="pubdate">
				<h3>Edited</h3>
				<p>
					<time datetime="<?php echo $draft->modified('c') ?>">
					<?php echo $draft->modified('F jS \a\t g:ia') ?>
					</time>
				</p>
			</div>
			
			<?php if($draft->metadata("tags")): ?>
			<div class="tags">
				<h3>Tagged</h3>
				<ul>
				<?php foreach($draft->metadata("tags") as $tag): ?>
					<li><a href="<?php echo $site_root ?>/posts/tagged/<?php echo urlencode($tag) ?>"><?php echo $tag ?></a></li>
				<?php endforeach ?>
				</ul>
			</div>
			<?php endif ?>
		</footer>
	</article>

<?php endforeach ?>

<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>