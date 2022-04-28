<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php $page->isChildOf($pages->find('terms')) || $page->isChildOf($pages->find('sources')) ? snippet('prevnext') : '' ; ?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<?php echo kirbytext($page->text()) ?>

		<?php $page->isChildOf($pages->find('terms')) || $page->isChildOf($pages->find('sources')) ? '' : snippet('submenu'); ?>
	
		<?php if($page->content()->has('Source')): ?>
		<?php if($page->source()->pages()->count() > 1) { echo '<h3>Sources</h3>'; } else { echo '<h3>Source</h3>'; } ?>
		<ul class="src">
			<?php $n=0; foreach($page->source()->pages() as $source): $n++; ?>
			<li><a href="<?php echo $source->url() ?>"><?php echo html($source->title()) ?></a></li>
			<?php endforeach ?>
		</ul>
		<?php endif ?>

		<?php if($page->content()->has('Related')): ?>
		<?php if($page->related()->pages()->count() > 1) { echo '<h3>Related Terms</h3>'; } else { echo '<h3>Related Term</h3>'; } ?>
		<ul class="src">
			<?php $n=0; foreach($page->related()->pages() as $related): $n++; ?>
			<li><a href="<?php echo $related->url() ?>"><?php echo html($related->title()) ?></a></li>
			<?php endforeach ?>
		</ul>
		<?php endif ?>

	</article>
</section>

<?php snippet('footer') ?>
