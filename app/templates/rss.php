<?php
header("Content-Type: application/xml;");
$posts = posts(10);
?>
<?= '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0">
	<channel>
	
		<title><?= $site_title ?></title>
		<link><?= $site_root ?></link>
		<description><?= $site_description ?></description>
		<language>en</language>
		<copyright>Copyright <?= date("Y") ?>, <?= $site_title ?></copyright>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<pubDate><?= date("r", $posts[0]->published) ?></pubDate>
		<lastBuildDate><?= date("r", $posts[0]->modified) ?></lastBuildDate>
		
		<? foreach($posts as $post): ?>
		<item>
		
			<title><?= $post->title ?></title>
			<link><?= $post->permalink ?></link>
			<pubDate><?= date("r", $post->published) ?></pubDate>
			<guid><?= $post->permalink ?></guid>
			
			<description><![CDATA[ <?= $post->content ?> ]]></description>

		</item>
		<? endforeach ?>
		
	</channel>
</rss>