<?php
	header('Content-Type:text/plain');
	header('Content-Disposition: attachment; filename="' . $filename . '-paperback.txt"');
	echo $title . PHP_EOL . PHP_EOL;
	if ($description != '') { echo ($description . PHP_EOL . PHP_EOL); }
	if ($version != '') { echo ('Version ' . $version . ' (' . $datestamp . ')'); } else { echo ('Published ' . $datestamp); }
	echo (PHP_EOL . PHP_EOL);
	foreach ($pages as $page) :
		snippet('paperback-export/content', compact('languages', 'page', 'prefix', 'fields'));
	endforeach;
?>
