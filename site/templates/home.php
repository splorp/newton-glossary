<?php snippet('header') ?>
<?php snippet('menu') ?>
<?php snippet('submenu') ?>

<section class="content">

  <article>
    <h1><?php echo html($page->title()) ?></h1>
    <?php
		$t = $pages->findByUID('terms')->children()->count();
	    $p = str_replace('_$t', $t, $page->text());
	?>
    <?php echo kirbytext($p) ?>
  </article>

</section>

<?php snippet('footer') ?>