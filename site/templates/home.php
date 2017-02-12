<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php
	$t = number_format(page('terms')->children()->count());
	$s = number_format(page('sources')->children()->count());
	$p = str_replace('_$t', $t, $page->text());
	$pp = str_replace('_$s', $s, $p);
?>

<section class="content">
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php echo kirbytext($pp) ?>
	</article>
</section>

<?php snippet('footer') ?>
