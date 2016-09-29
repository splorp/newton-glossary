<nav class="submenu">
	<ul>
	<?php if($page->hasPrev() && !$page->prev()->isErrorPage()): ?>
		<li><a href="<?php echo $page->prev()->url() ?>" title="View the previous term">Previous</a></li>
	<?php endif ?>
	<?php if($page->hasNext() && !$page->next()->isErrorPage()): ?>
		<li><a href="<?php echo $page->next()->url() ?>" title="View the next term">Next</a></li>
	<?php endif ?>
	</ul>
</nav>
