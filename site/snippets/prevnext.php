<nav class="submenu">
	<ul>
	<?php if($page->hasPrev() && !$page->prev()->isErrorPage()) : ?>
		<li><a href="/<?php echo $page->prev() ?>" title="Previously: <?php echo $page->prev()->title() ?>">Previous</a></li>
	<?php else: ?>
		<li>Previous</li>
	<?php endif ?>
	<?php if($page->hasNext() && !$page->next()->isErrorPage()) : ?>
		<li><a href="/<?php echo $page->next() ?>" title="Next up: <?php echo $page->next()->title() ?>">Next</a></li>
	<?php else: ?>
		<li>Next</li>
	<?php endif ?>
	</ul>
</nav>
