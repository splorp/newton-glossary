<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php
	/* Nab the number of term and source pages */
	$t = number_format(page('terms')->children()->count());
	$s = number_format(page('sources')->children()->count());
	/* Replace the term count placeholder in the page text */
	$p = str_replace('_$t', $t, $page->text());
	/* Replace the source count placeholder in the page text */
	$pp = str_replace('_$s', $s, $p);
?>

<section>
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php echo kirbytext($pp) ?>
	</article>
</section>

<?php snippet('footer') ?>
