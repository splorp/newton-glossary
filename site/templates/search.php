<?php snippet('header') ?>
<?php snippet('menu') ?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<?php echo kirbytext($page->text()) ?>

		<form<?php echo $pagination->hasPages() ? ' action ="' . url('search') . '"' : ''; ?>>
			<input type="search" name="q" placeholder="What are you looking for?" value="<?php echo esc($query) ?>" autofocus />
			<input type="submit" value="Go!" />
		</form>

		<?php if($results->count() > 0) : ?>
		<?php if($results->count() == 1) : go($results); endif ?>
		<h3><?php echo $pagination->items() . ' item'; echo ($pagination->items() > 1) ? 's' : ''; echo ' found'; ?></h3>
		<ul class="rslt">
			<?php foreach($results as $result): ?>
			<li><a href="<?php echo $result->url() ?>"><?php echo $result->title()->html() ?></a><br />
			<?php echo $result->text()->excerpt(140) ?></li>
			<?php endforeach ?>
		</ul>

		<?php elseif($query != '') : ?>
			<h2>No items found</h2>
		<?php endif ?>

	</article>
</section>

<?php if($pagination->hasPages()) : ?>
<?php snippet('pagination', array('pagination' => $results->pagination())) ?>
<?php endif ?>

<?php snippet('footer') ?>
