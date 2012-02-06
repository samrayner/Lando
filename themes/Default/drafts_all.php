<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<h1>Drafts</h1>

<?php foreach(drafts() as $Draft): ?>

	<article>
		<h1><a href="<?php echo $Draft->permalink() ?>"><?php echo $Draft->title() ?></a></h1>
		<footer>
			<div class="pubdate">
				<h3>Last Edited</h3>
				<p>
					<time datetime="<?php echo $Draft->modified('c') ?>">
					<?php echo $Draft->modified('F jS \a\t g:ia') ?>
					</time>
				</p>
			</div>
			
			<?php if($Draft->metadata("tags")): ?>
			<div class="tags">
				<h3>Tagged</h3>
				<ul>
				<?php foreach($Draft->metadata("tags") as $tag): ?>
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