<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<h1><?php echo $Current->title() ?></h1>

<div id="intro">
<?php echo $Current->content() ?>
</div>

<?php foreach(posts(5) as $Post): ?>

	<article>
		<h1><a href="<?php echo $Post->permalink() ?>"><?php echo $Post->title() ?></a></h1>
		<?php echo truncate_html($Post->content(), 200) ?>
		<footer>
			<div class="pubdate">
				<h3>Posted</h3>
				<p>
					<time datetime="<?php echo $Post->published('c') ?>">
						<?php echo $Post->published('F jS Y') ?>
					</time>
			</p>
			</div>
			
			<?php if($Post->metadata("tags")): ?>
			<div class="tags">
				<h3>Tagged</h3>
				<ul>
				<?php foreach($Post->metadata("tags") as $tag): ?>
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