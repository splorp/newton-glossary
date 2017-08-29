<?php snippet('header') ?>
<?php snippet('menu') ?>

<section>
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php echo kirbytext($page->text()) ?>
	</article>
</section>

<?php snippet('footer') ?>
