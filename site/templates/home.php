<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php
	/* Nab the number of term and source pages */
	$t = number_format(page('terms')->children()->count());
	$s = number_format(page('sources')->children()->count());
	/* Replace placeholders in the page text */
	$p = str_replace(array('_$t','_$s'), array($t,$s), $page->text());
?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<?php echo kirbytext($p) ?>
	</article>
</section>

<?php snippet('footer') ?>
