<?php

// Determine the current page and whether it has child pages

$open  = $pages->findOpen();
$items = ($open) ? $open->children() : false;

// Sort child pages by title, then group alphabetically
// SORT_STRING|SORT_FLAG_CASE sorts numerals then alphabetic characters in case-insensitive order
// An integer may be used in the last parameter of the sortBy() function to specify the sort method:
// 0 = SORT_REGULAR
// 1 = SORT_NUMERIC
// 2 = SORT_STRING

$alphabetise = $page->children()->sortBy('slug', 'asc', SORT_STRING|SORT_FLAG_CASE)->group(fn ($item) => str::upper(str::substr($item->slug(),0,1)));

// Create alphabetical list of pages, with subheadings

?>

<?php if($items && $items->count()): ?>
<nav class="submenu alphabetical">
	<?php foreach($alphabetise as $letter => $items): ?>
	<h3><?php echo str::upper($letter) ?></h3>
	<ul>
		<?php foreach($items as $item): ?>
		<li><a href="<?php echo $site->url() . '/' . $item ?>"><?php echo html($item->title()) ?></a></li>
		<?php endforeach ?>
	</ul>
	<?php endforeach ?>
</nav>
<?php endif ?>
