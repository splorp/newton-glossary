<?php if($page->description() != '') { $page_description = $page->description(); } else { $page_description = excerpt(kirbytext($page->text()), 300); } ?>

<?php snippet('header', array('title' => $page->title(), 'page_description' => $page_description)); ?>
<?php snippet('menu') ?>

<section class="content">
	<article>
		<h1><?php echo html($page->title()) ?></h1>
		<?php echo kirbytext($page->text()) ?>

		<form>
			<input type="search" name="q" placeholder="Enter a search term …" value="<?php echo esc($query) ?>" />
			<input type="submit" value="Search" />
		</form>

		<?php if($results != '') { ?>
		<h2>Results</h2>
		<ul>
			<?php foreach($results as $result): ?>
			<li><a href="<?php echo $result->url() ?>"><?php echo $result->title()->html() ?></a></li>
			<?php endforeach ?>
		</ul>
		<?php } else { ?>
			<p>Nothing matching <strong>“<?php echo esc($query) ?>”</strong> was found.</p>
		<?php } ?>
	</article>
</section>

<?php snippet('footer') ?>
