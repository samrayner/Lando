<?php
include_once "classes/autoload.php";
$dp = new DropPub();

if(file_exists("functions.php"))
	include "functions.php";

//trim leading slash to make relative
$template = $dp->trimSlashes($dp->getThemeDir())."/rss.php";

header("Content-Type: application/xml;");

if(file_exists($template)) {
	set_include_path(get_include_path().":".$_SERVER['DOCUMENT_ROOT'].$dp->getThemeDir());
	include $template;
	exit();
}

$posts = $dp->getPosts(10,0);
?>
<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0">
	<channel>
	
		<title><?= $dp->getSiteTitle() ?></title>
		<link><?= $dp->getSiteRoot() ?></link>
		<description><?= $dp->getSiteDescription() ?></description>
		<language>en</language>
		<copyright>Copyright <? echo date("Y"); ?>, <?= $dp->getDBName() ?></copyright>
		<managingEditor><?= $dp->getDBEmail() ?> (<?= $dp->getDBName() ?>)</managingEditor>
		<webMaster><?= $dp->getDBEmail() ?> (<?= $dp->getDBName() ?>)</webMaster>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<pubDate><?= date("r", $dp->getPublished($posts[0])) ?></pubDate>
		<lastBuildDate><?= date("r", $dp->getModified($posts[0])) ?></lastBuildDate>
		
		<? foreach($posts as $permalink): ?>
		<item>
		
			<title><?= $dp->getTitle($permalink) ?></title>
			<link><?= $dp->getSiteRoot().$permalink ?></link>
			<pubDate><?= date("r", $dp->getPublished($permalink)) ?></pubDate>
			<guid><?= $dp->getSiteRoot().$permalink ?></guid>
			
			<description><![CDATA[ <?= $dp->getContent($permalink) ?> ]]></description>

		</item>
		<? endforeach ?>
		
	</channel>
</rss>