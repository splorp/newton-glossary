<nav class="submenu">
  <ul>
    <?php if($page->hasPrev() && $page->prev()->hasPrev() && !$page->prev()->prev()->isErrorPage()): ?>
      <li><a href="<?php echo $page->prev()->prev()->url() ?>" title="View the previous term"><?php echo $page->prev()->prev()->title() ?></a></li>
    <?php endif ?>
    <?php if($page->hasPrev() && !$page->prev()->isErrorPage()): ?>
      <li><a href="<?php echo $page->prev()->url() ?>" title="View the previous term"><?php echo $page->prev()->title() ?></a></li>
    <?php endif ?>
      <li><a href="<?php echo $page->url() ?>" class="active" title="You are here"><?php echo $page->title() ?></a></li>
    <?php if($page->hasNext() && !$page->next()->isErrorPage()): ?>
      <li><a href="<?php echo $page->next()->url() ?>" title="View the next term"><?php echo $page->next()->title() ?></a></li>
    <?php endif ?>
    <?php if($page->hasNext() && $page->next()->hasNext() && !$page->next()->next()->isErrorPage()): ?>
      <li><a href="<?php echo $page->next()->next()->url() ?>" title="View the next term"><?php echo $page->next()->next()->title() ?></a></li>
    <?php endif ?>
  </ul>
</nav>