<?php snippet('header') ?>
<?php snippet('menu') ?>

<?php $page->isChildOf($pages->find('terms')) || $page->isChildOf($pages->find('sources')) ? snippet('prevnext') : '' ; ?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<?php echo $page->text()->kirbytext() ?>

		<?php $page->isChildOf($pages->find('terms')) || $page->isChildOf($pages->find('sources')) ? '' : snippet('submenu'); ?>
	
		<?php if($page->source()->exists()): ?>
		<?php if($page->source()->toPages()->count() > 1) { echo '<h3>Sources</h3>'; } else { echo '<h3>Source</h3>'; } ?>
		<ul class="src">
			<?php $n=0; foreach($page->source()->toPages() as $source): $n++; ?>
			<li><a href="<?php echo $source->url() ?>"><?php echo html($source->title()) ?></a></li>
			<?php endforeach ?>
		</ul>
		<?php endif ?>

		<?php if($page->related()->exists()): ?>
		<?php if($page->related()->toPages()->count() > 1) { echo '<h3>Related Terms</h3>'; } else { echo '<h3>Related Term</h3>'; } ?>
		<ul class="src">
			<?php $n=0; foreach($page->related()->toPages() as $related): $n++; ?>
			<li><a href="<?php echo $related->url() ?>"><?php echo html($related->title()) ?></a></li>
			<?php endforeach ?>
		</ul>
		<?php endif ?>

	</article>
</section>

<?php snippet('footer') ?>
