<?php snippet('header', array('section' => 'term', 'title' => $page->title(), 'page_description' => str::unhtml(kirbytext($page->text())))); ?>
<?php snippet('menu') ?>
<?php $page->isChildOf($pages->find('terms')) ? snippet('prevnext') : snippet('submenu'); ?>

<section class="content">
  <article>
    <h1><?php echo html($page->title()) ?></h1>
    <?php echo kirbytext($page->text()) ?>

    <?php if($page->related()): ?>
      <h2>Related Terms</h2>
      <ul>
        <?php foreach(related($page->related()) as $related): ?>
          <li><a href="<?php echo $related->url() ?>"><?php echo html($related->title()) ?></a></li>
        <?php endforeach ?>
      </ul>
    <?php endif ?>

  </article>
</section>

<?php snippet('footer') ?>