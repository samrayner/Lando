<?php include "inc/head.php" ?>
<?php include "inc/header.php" ?>

<?php if($Current->parent()) echo page_breadcrumbs() ?>

<h1><?php echo $Current->title() ?></h1>
<?php echo $Current->content() ?>

<?php include "inc/footer.php" ?>
<?php include "inc/foot.php" ?>