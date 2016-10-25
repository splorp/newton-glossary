<?php if($page->description() != '') { $page_description = $page->description(); } else { $page_description = excerpt(kirbytext($page->text()), 300); } ?>

<?php snippet('header', array('title' => $page->title(), 'page_description' => $page_description)); ?>
<?php snippet('menu') ?>

<section class="content">
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php echo kirbytext($page->text()) ?>

		<form>
			<input type="search" name="q" placeholder="What are you looking for?" value="<?php echo esc($query) ?>" />
			<input type="submit" value="Go!" />
		</form>

		<?php if($results->count() > 0) : ?>
		<h2><?php echo $pagination->items() . ' item'; echo ($pagination->items() > 1) ? 's' : ''; echo ' found'; ?></h2>
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

<?php snippet('footer') ?>
