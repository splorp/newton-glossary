<?php if($page->description() != '') { $page_description = $page->description(); } else { $page_description = excerpt(kirbytext($page->text()), 300); } ?>

<?php snippet('header', array('title' => $page->title(), 'page_description' => $page_description)); ?>
<?php snippet('menu') ?>

<section class="content">
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php
			$t = $pages->findByUID('terms')->children()->count();
			$s = $pages->findByUID('sources')->children()->count();
			$p = str_replace('_$t', $t, $page->text());
			$pp = str_replace('_$s', $s, $p);
		?>
		<?php echo kirbytext($pp) ?>
	</article>
</section>

<?php snippet('footer') ?>
