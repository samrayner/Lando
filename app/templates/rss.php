<?php
header("Content-Type: application/xml;");
$posts = posts(10);
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss version="2.0">
	<channel>
	
		<title><?php echo $site_title ?></title>
		<link><?php echo $site_root ?></link>
		<description><?php echo $site_description ?></description>
		<language>en</language>
		<copyright>Copyright <?php echo date("Y") ?>, <?php echo $site_title ?></copyright>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
		<pubDate><?php echo date("r", $posts[0]->published) ?></pubDate>
		<lastBuildDate><?php echo date("r", $posts[0]->modified) ?></lastBuildDate>
		
		<? foreach($posts as $post): ?>
		<item>
		
			<title><?php echo $post->title ?></title>
			<link><?php echo $post->permalink ?></link>
			<pubDate><?php echo date("r", $post->published) ?></pubDate>
			<guid><?php echo $post->permalink ?></guid>
			
			<description><![CDATA[ <?php echo $post->content ?> ]]></description>

		</item>
		<? endforeach ?>
		
	</channel>
</rss>