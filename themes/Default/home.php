<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<h1><?php echo $current->title() ?></h1>

<div id="intro">
<?php echo $current->content() ?>
</div>

<?php foreach(posts(5) as $post): ?>

	<article>
		<h1><a href="<?php echo $post->permalink() ?>"><?php echo $post->title() ?></a></h1>
		<?php echo truncate_html($post->content(), 200) ?>
		<footer>
			<div class="pubdate">
				<h3>Posted</h3>
				<p>
					<time datetime="<?php echo $post->published('c') ?>">
						<?php echo $post->published('F jS Y') ?>
					</time>
			</p>
			</div>
			
			<?php if($post->metadata("tags")): ?>
			<div class="tags">
				<h3>Tagged</h3>
				<ul>
				<?php foreach($post->metadata("tags") as $tag): ?>
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