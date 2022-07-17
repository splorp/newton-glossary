<?php
	echo $prefix . $page->title() . PHP_EOL . PHP_EOL;
	$buffer = $page->text()->kirbytext();
	// Standardize line breaks between block elements
	$buffer = preg_replace('/>\s?[\r\n]\s?</', '>' . PHP_EOL . PHP_EOL . '<', $buffer);
	// Remove line breaks following <br> tags
	$buffer = str_replace(array("<br>" . PHP_EOL . PHP_EOL, "<br />" . PHP_EOL . PHP_EOL), PHP_EOL, $buffer);
	// Remove line breaks preceeding <img> tags
	$buffer = preg_replace('/\n\n<p><img .+><\/p>/', '', $buffer);
	// Remove all remaining tags
	$buffer = html_entity_decode(strip_tags($buffer));
	echo $buffer . PHP_EOL . PHP_EOL;
	if($page->source()->exists()) {
		if($page->source()->toPages()->count() > 1) { echo 'Sources: ' . PHP_EOL; } else { echo 'Source: '; }
		$n=0; foreach($page->source()->toPages() as $source): $n++;
		echo $source->title() . PHP_EOL;
		endforeach;
		echo PHP_EOL;
	}
?>
