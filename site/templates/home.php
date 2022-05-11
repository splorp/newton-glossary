<?php snippet('header') ?>
<?php snippet('menu') ?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<?php echo $page->text()->kirbytext() ?>
	</article>
</section>

<?php snippet('footer') ?>
