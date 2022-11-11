# Alphabetise Plugin

## What is it?

The `alphabetise` plugin takes a given [Kirby CMS](http://getkirby.com/) *page* array or *tag* array and returns an alphabetised or numbered array that you can then display or further process. It has been updated to work with Kirby CMS v3+.


## Installation


### Clone or download

1. [Clone](https://github.com/shoesforindustry/kirby-plugins-alphabetise.git) or [download](https://github.com/shoesforindustry/kirby-plugins-alphabetise/archive/master.zip) this repository.
2. Unzip / Move the folder to `site/plugins` and rename it to `alphabetise`.


## How to use it?

### 1. Alphabetical list of child pages using page title as the key:

* **A**
  * Aa 
  * Ab 
* **B**
  * Ba 
  * Bb
+ **1**
  + 1a
  + 1b
+ **2**
  + 2a
  + 2b


The first argument you pass is the sorted **page** array you want to *alphabetise*. The second array's **key** argument determines what to *alphabetise* by. It should be a string like a page 'title'. The values passed to 'sortBy' and 'key' usually are the same.

In your template, call it like this:

```php

<?php $alphabetise = alphabetise($page->children()->listed()->sortby('title'), array('key' => 'title')); ?>

```


You then want to loop through the returned results and display them, for example:

```php
<?php foreach($alphabetise as $letter => $items) : ?>
  <h2><?= str::upper($letter) ?></h2>
  <ul>
  <?php foreach($items as $item): ?>
    <li>
      <a href="<?= $item->url()?>">
        <?= $item->title()?>
      </a>
   	</li>
  <?php endforeach ?>
  </ul>
  <hr>
<?php endforeach ?>
```

Result:

+ **A**
  + Aa
  + Ab
+ **B**
  + Ba
  + Bb
+ **1**
  + 1a
  + 1b
+ **2**
  + 2a
  + 2b

### 3. Set 'orderBy' key:

Version 0.0.9 added a key to alter how the array appears, by default letters before numbers, e.g.

+ A
+ B
+ 1
+ 2

Or you can set the `orderby` key to `SORT_STRING` so numbers are listed first, e.g.

+ 1
+ 2
+ A
+ B

```php
<?php $alphabetise = alphabetise($page->children()->listed()->sortby('title'), array('key' => 'title', 'orderby'=>SORT_STRING));?>

```


## Notes:

The array whose *key* your are trying to sort by should of course only contain letters of the alphabet, otherwise problems may occur.

Also the code (explode) uses a `~` tilde - if you use this in your *key*, especially at the beginning of the string, then you could run into sorting problems. You could of course manually change it if required.

*We are using `ksort`, so other `sort_flags` might be possible, but are untested!*

**The `orderby` key is not a string!**



## Author
Russ Baldwin  
[shoesforindustry.net](shoesforindustry.net)
