<? if(admin_cookie()): ?>

<div id="admin-links">
	<?php if(preg_match('~^(posts|drafts)~', $template, $types)): ?>
		<a href="<?php echo $site_root ?>/admin/update/?type=<?php echo $types[1] ?>&amp;redirect=<?php echo current_path() ?>">Update</a>
	<?php endif ?>

	<?php if($current->path()): ?>
		<a href="<?php echo $site_root ?>/admin/update/single.php?path=<?php echo current_path() ?>">Update</a>
	<?php endif ?>

	<?php if($template == "draft"): ?>
		<a href="<?php echo $site_root ?>/admin/publish.php?slug=<?php echo $current->slug() ?>">Publish</a>
	<?php endif ?>

	<?php if(in_array($template, array("posts-all", "posts-by-date", "posts-by-tag", "post", "draft"))): ?>
		<a href="<?php echo $site_root ?>/drafts/">Drafts</a>
	<?php endif ?>

	<a href="<?php echo $site_root ?>/admin/">Admin</a>
</div>

<?php endif ?>