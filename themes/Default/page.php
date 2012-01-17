<?php include "inc/head.php" ?>

<body id="<?php echo $current->slug() ?>">

<?php include "inc/header.php" ?>

<section id="primary">
	<h1><?php echo $current->title() ?></h1>
	<?php echo $current->content() ?>
</section>

<?php include "inc/footer.php" ?>

</body>

<?php include "inc/foot.php" ?>