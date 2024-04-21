<?php

// Determine the current page and whether it has child pages

$open  = $pages->findOpen();
$items = ($open) ? $open->children() : false;

// Sort child pages by title, then group alphabetically

$alphabetise = $page->children()->sortBy('title', 'asc')->group(fn ($item) => str::upper($item->title()->value()[0]));

// Create alphabetical list of pages, with subheadings

?>

<?php if($items && $items->count()): ?>
<nav class="submenu alphabetical">
	<?php foreach($alphabetise as $letter => $items): ?>
	<h3><?php echo str::upper($letter) ?></h3>
	<ul>
		<?php foreach($items as $item): ?>
		<li><a href="/<?php echo $item ?>"><?php echo html($item->title()) ?></a></li>
		<?php endforeach ?>
	</ul>
	<?php endforeach ?>
</nav>
<?php endif ?>
