<?php include "inc/head.php" ?>
<body id="home">

<?php include "inc/header.php" ?>

<section id="primary">

	<?php echo $current->content() ?>

	<?php 
		$snippet = snippet("emails.csv");
		echo $snippet->content();
	?>
	
</section>

<?php include "inc/footer.php" ?>

</body>
<?php include "inc/foot.php" ?>