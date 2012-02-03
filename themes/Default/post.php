<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<article>
	<h1><?php echo $Current->title() ?></h1>
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
		
		<?php if($tags = $Current->metadata("tags")): ?>
		<div class="tags">
			<h3>Tagged</h3>
			<ul>
			<?php foreach($tags as $tag): ?>
				<li><a href="<?php echo $site_root ?>/posts/tagged/<?php echo urlencode($tag) ?>"><?php echo $tag ?></a></li>
			<?php endforeach ?>
			</ul>
		</div>
		<?php endif ?>

		<?php if($Next = $Current->next()): ?>
		<div class="next">
			<h3>Next</h3>
			<p><a rel="next" href="<?php echo $Next->permalink() ?>"><?php echo $Next->title() ?></a></p>
		</div>
		<?php endif ?>

		<?php if($Prev = $Current->previous()): ?>
		<div class="previous">
			<h3>Previous</h3>
			<p><a rel="prev" href="<?php echo $Prev->permalink() ?>"><?php echo $Prev->title() ?></a></p>
		</div>
		<?php endif ?>
	</footer>
</article>

<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>