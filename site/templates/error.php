<?php snippet('header') ?>
<?php snippet('menu') ?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<?php echo kirbytext($page->text()) ?>
	</article>
</section>

<?php snippet('footer') ?>
