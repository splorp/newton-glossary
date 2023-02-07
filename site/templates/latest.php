<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php $latest_pages = pages('terms', 'sources')->children()->filterBy('date', '!=', '')->sortBy(function ($pages) {
	return $pages->date()->toDate();
}, 'desc')->limit(100); ?>

<section>
	<article>
		<h2 class="rslt"><?php echo html($page->title()) ?></h2>
		<?php echo $page->text()->kirbytext() ?>
		<ul>
			<?php foreach($latest_pages as $latest): ?>
			<li><a href="/<?php echo $latest ?>"><?php echo $latest->title()->html() ?></a><br />
			<span class="mouseprint">New <?php echo rtrim($latest->parent()->slug(), "s") ?> added <?php echo $latest->date('d F Y') ?></span></li>
			<?php endforeach ?>
		</ul>
	</article>
</section>

<?php snippet('footer') ?>
