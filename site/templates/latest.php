<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php $latest_pages = $pages->find('terms', 'sources')->children()->filterBy('date', '!=', '')->sortBy('date', 'desc')->limit(100); ?>

<section>
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
