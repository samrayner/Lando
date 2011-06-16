<?php include "inc/head.php" ?>
<body id="home">

<header>
	<h1><a href="<?= $dp->getSiteRoot() ?>"><?= $dp->getSiteTitle() ?></a></h1>	
	<? $dp->printPageNav() ?>
</header>

<section id="primary">

	<? 
		$year 	= isset($_GET["year"]) 	? $_GET["year"] 	: NULL;
		$month 	= isset($_GET["month"]) ? $_GET["month"] 	: NULL;
		$day 		= isset($_GET["day"]) 	? $_GET["day"] 		: NULL;
		
		$dateStr = "Archive";
		
		if($year) {
			$dateStr = "from ".date("Y", mktime(0,0,0,1,1,$year));
			if($month) {
				$dateStr = "from ".date("F, Y", mktime(0,0,0,$month,1,$year));
				if($day) {
					$dateStr = "from ".date("F jS, Y", mktime(0,0,0,$month,$day,$year));
				}
			}
		}
	?>

	<h1>Posts <?= $dateStr ?></h1>
	
	<? $posts = $dp->getPosts(0, 0, $year, $month, $day);
		 foreach($posts as $i => $permalink): ?>
	
		<? if(!$year and (!isset($posts[$i-1]) or date('Y', $dp->getPublished($posts[$i])) != date('Y', $dp->getPublished($posts[$i-1])))): ?>
			<h2><?= date('Y', $dp->getPublished($posts[$i])) ?></h2>
		<? endif ?>
	
		<? if(!$month and (!isset($posts[$i-1]) or date('n', $dp->getPublished($posts[$i])) != date('n', $dp->getPublished($posts[$i-1])))): ?>
			<h3><?= date('F', $dp->getPublished($posts[$i])) ?></h3>
		<? endif ?>

		<article>
			<h1><a href="<?= $dp->getSiteRoot().$permalink ?>"><?= $dp->getTitle($permalink) ?></a></h1>
			<footer>
				<p>Posted 
					<time pubdate datetime="<?= date('c', $dp->getPublished($permalink)) ?>">
						<?= date('F jS \a\t g:ia', $dp->getPublished($permalink)) ?>
					</time>
				</p>
			</footer>
		</article>

	<? endforeach ?>
	
</section>

<footer>
	<?= $dp->getSnippet("footer") ?>
</footer>

</body>
<?php include "inc/foot.php" ?>