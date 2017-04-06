<?php if($page->description() != '') { $page_description = $page->description(); } else { $page_description = excerpt(kirbytext($page->text()), 300); } ?>

<?php snippet('header', array('title' => $page->title(), 'page_description' => $page_description)); ?>
<?php snippet('menu') ?>

<?php $latest_pages = $pages->find('terms', 'sources')->children()->filterBy('date', '!=', '')->sortBy('date', 'desc')->limit(30); ?>

<section class="content">
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php echo kirbytext($page->text()) ?>
		<ul class="rslt">
			<?php foreach($latest_pages as $latest): ?>
			<li><a href="<?php echo $latest->url() ?>"><?php echo $latest->title()->html() ?></a><br />
			<span class="mouseprint">New <?php echo rtrim($latest->parent()->slug(), "s") ?> added <?php echo $latest->date('d F Y') ?></span></li>
			<?php endforeach ?>
		</ul>
	</article>
</section>

<?php snippet('footer') ?>
