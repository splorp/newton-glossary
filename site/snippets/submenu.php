<?php

// Determine which first level page in open
$open  = $pages->findOpen();
$items = ($open) ? $open->children() : false;

// Sort pages by title
$sorted = $page->children()->sortBy('title');

// Alphabetize pages by title
$alphabetise = alphabetise($sorted, array('key' => 'title', 'orderby' => SORT_REGULAR));
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
