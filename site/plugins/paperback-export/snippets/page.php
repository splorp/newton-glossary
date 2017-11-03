<?php echo '@@TOC ' . $page->title() . PHP_EOL . PHP_EOL ?>
<?php $buffer = str_replace(array("</p>\r<p>", "</p>\r <p>", "</p> \r<p>", "</p>\n<p>", "</p>\n <p>", "</p> \n<p>", "</p>\r\n<p>", "</p>\r\n <p>", "</p> \r\n<p>"), '</p>' . PHP_EOL . PHP_EOL . '<p>', $page->text()->kirbytext()); ?>
<?php echo html_entity_decode(strip_tags($buffer)) . PHP_EOL . PHP_EOL ?>
<?php if($page->content()->has('Source')): ?>
<?php if($page->source()->pages()->count() > 1) { echo 'Sources: '; } else { echo 'Source: '; } ?>
<?php $n=0; foreach($page->source()->pages() as $source): $n++; ?>
<?php echo $source->title() . PHP_EOL ?>
<?php endforeach ?>
<?php echo PHP_EOL ?>
<?php endif ?>
