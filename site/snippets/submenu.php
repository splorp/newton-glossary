<?php 

// Find the open or active page on the first level
$open  = $pages->findOpen();
$items = ($open) ? $open->children() : false; 
// Set up alphabetise plugin
$alphabetise = alphabetise($page->children()->sortby('sort'), array('key' => 'sort'));

?>
<?php if($items && $items->count()): ?>
<nav class="submenu alphabetical">
<?php foreach($alphabetise as $letter => $items): ?>
  <h2><?php echo strtoupper($letter) ?></h2>
  <ul>
    <?php foreach($items as $item): ?>
    <li><a href="<?php echo $item->url() ?>"><?php echo html($item->title()) ?></a></li>
    <?php endforeach ?>            
  </ul>
<?php endforeach ?>
</nav>
<?php endif ?>
