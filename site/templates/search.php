<?php snippet('header') ?>
<?php snippet('menu') ?>

<section>
	<article>
		<h2><?php echo html($page->title()) ?></h2>
		<details>
			<summary><a>Need help?</a></summary>
			<?php echo $page->text()->kirbytext() ?>
		</details>
		<form action="<?= $site->url() . '/' . $page ?>">
			<input type="search" name="q" placeholder="What are you looking for?" value="<?= html($query) ?>" autofocus>
			<input type="submit" value="Go!">
		</form>

		<?php if($pagination->total() > 0) : ?>
		<?php if($pagination->total() == 1) : go($results); endif ?>
		<h3 class="rslt"><?php echo $pagination->total() . ' item'; echo ($pagination->total() > 1) ? 's' : ''; echo ' found'; ?></h3>
		<ul>
		<?php foreach($results as $result): ?>
			<li><span class="mouseprint"><?php if($result->parent()) : echo Str::studly(rtrim($result->parent()->slug(), "s")); else : echo("Page"); endif; ?></span><br>
			<a href="<?php echo $site->url() . '/' . $result ?>"><?php echo $result->title()->html() ?></a><br>
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
