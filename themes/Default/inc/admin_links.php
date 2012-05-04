<? if(admin_cookie()): ?>

<div id="admin-links">
	<?php if(preg_match('~^(posts|drafts)~', $template, $types)): ?>
		<a href="<?php echo $Lando->config["site_root"] ?>/admin/update/?type=<?php echo $types[1] ?>&amp;redirect=<?php echo current_path() ?>&amp;delete=0">Update <?php echo $types[1] ?></a>
	<?php endif ?>

	<?php if($Current->path()): ?>
		<?php 
			$seg = path_segment(1);
			$content_type = ($seg == "posts" || $seg == "drafts") ? substr($seg, 0, -1) : "page";
		?>
		<a href="<?php echo $Lando->config["site_root"] ?>/admin/update/single.php?path=<?php echo current_path() ?>">Update <?php echo $content_type ?></a>
	<?php endif ?>

	<?php if($template == "draft"): ?>
		<a href="<?php echo $Lando->config["site_root"] ?>/admin/publish_draft.php?slug=<?php echo $Current->slug() ?>">Publish</a>
	<?php endif ?>

	<?php if(in_array($template, array("posts_all", "posts_by_date", "posts_by_tag", "post", "draft"))): ?>
		<a href="<?php echo $site_root ?>/drafts/">Drafts</a>
	<?php endif ?>

	<a href="<?php echo $Lando->config["site_root"] ?>/admin/">Admin</a>
</div>

<?php endif ?>