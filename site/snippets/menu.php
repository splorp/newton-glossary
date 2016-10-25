<?php
	$random = $pages->find('terms')->children()->shuffle()->first();
?>

<nav class="menu">
	<ul>
	<?php foreach($pages->visible() as $p): ?>
		<li><a href="<?php echo $p->url() ?>"<?php echo ($p->isOpen()) ? ' class="active"' : '' ?>><?php echo html($p->title()) ?></a></li>
	<?php endforeach ?>
		<li><a href="<?php echo $random->url() ?>">Random</a></li>
		<li><a href="/search"<?php echo (page('search')->isOpen()) ? ' class="active"' : '' ?>>Search</a></li>
	</ul>
</nav>
