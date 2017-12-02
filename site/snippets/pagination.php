<nav>
	<ul>
	<?php if($pagination->hasPrevPage()): ?>
		<li><a href="<?php echo $pagination->prevPageURL() ?>" title="Previous page of search results">Previous</a></li>
	<?php else: ?>
		<li>Previous</li>
	<?php endif ?>
	<?php if($pagination->hasNextPage()): ?>
		<li><a href="<?php echo $pagination->nextPageURL() ?>" title="Next page of search results">Next</a></li>
	<?php else: ?>
		<li>Next</li>
	<?php endif ?>
	</ul>
</nav>
