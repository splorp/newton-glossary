<?php
	$random = $pages->find('terms')->children()->shuffle()->first();
?>

<nav class="menu">
	<ul>
	<?php foreach($pages->listed()->not('about', 'changes') as $p): ?>
		<li><a href="<?php echo $p->url() ?>" title="<?php echo $p->description() ?>"<?php echo ($p->isOpen()) ? ' class="active"' : '' ?>><?php if($p->menu() != '') { echo html($p->menu()); } else { echo html($p->title()); } ?></a></li>
	<?php endforeach ?>
		<li><a href="<?php echo $random->url() ?>" title="Feeling lucky? View a random term from the glossary.">Random</a></li>
		<li><a href="/search" title="<?php echo (page('search')->description()) ?>"<?php echo (page('search')->isOpen()) ? ' class="active"' : '' ?>>Search</a></li>
	</ul>
</nav>
