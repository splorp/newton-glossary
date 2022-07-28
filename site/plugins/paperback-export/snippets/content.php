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
	// Determine other content fields
	foreach ($fields as $fieldname => $fieldtype) :
 		if($page->content()->get($fieldname)->isNotEmpty()) {
 			echo ucwords($fieldname) . ': ';
 			if($fieldtype == 'related') {
				$n = false;
				foreach($page->content()->get($fieldname)->toPages() as $fieldpage):
					if ($n) : echo ', ';
					endif;
					echo $fieldpage->title();
					$n = true;
				endforeach;
				echo PHP_EOL . PHP_EOL;
			} else {
 				echo $page->content()->get($fieldname) . PHP_EOL . PHP_EOL;
			}
		}
	endforeach;
?>
