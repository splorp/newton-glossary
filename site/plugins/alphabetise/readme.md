# Kirby Alphabetise Plugin

The Kirby Alphabetise Plugin processes a [Kirby CMS](http://getkirby.com/) `$page` or `$tag` array and returns a new array that is organized alphabetically or numerically with labeled subsections.

This project is based on a fork of [shoesforindustry/kirby-plugins-alphabetise](https://github.com/shoesforindustry/kirby-plugins-alphabetise) by [Russ Baldwin](https://shoesforindustry.net/).

Compatible with both Kirby 3 and Kirby 4.


## Installation

### Clone

1. [Clone](https://github.com/splorp/kirby-alphabetise.git) this repository.
2. Move the folder to `site/plugins`
3. Rename the folder to `alphabetise`

### Download
 
1. [Download](https://github.com/splorp/kirby-alphabetise/archive/refs/heads/master.zip) this repository.
2. Decompress the `master.zip` archive.
3. Move the folder to `site/plugins`
4. Rename the folder to `alphabetise`


## Usage

### 1. Create a list of child pages using a key value

The first argument you pass is the sorted `$page` or `$tag` array you want to alphabetise.

The `key` argument of the second array determines what to alphabetise by.

This argument should be a string like `title` or `date`. <sup>[Note 1](#info_1)</sup>

The values passed to `sortBy` and `key` are usually the same.

For example, you would include this in your template:

```php
<?php $alphabetise = alphabetise($page->children()->listed()->sortby('title'), array('key' => 'title')); ?>
```

### 2. Loop through the returned results and display them

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
<?php endforeach ?>
```

Result:

+ **1**
  + 1a
  + 1b
+ **2**
  + 2a
  + 2b
+ **A**
  + Aa
  + Ab
+ **B**
  + Ba
  + Bb

### 3. Optionally set the sort order

The sort order is specified by using the `orderby` flag when calling the `alphabetise` function. <sup>[Note 2](#info_2)</sup>

By default, numbers and symbols are listed before letters. <sup>[Note 3](#info_3)</sup>

This is the same result as setting the `orderby` flag to `SORT_REGULAR` or `SORT_STRING` <sup>[Note 4](#info_4)</sup>

+ 1
+ 2
+ @
+ A
+ B

For example:

```php
<?php $alphabetise = alphabetise($page->children()->listed()->sortby('title'), array('key' => 'title', 'orderby'=>SORT_REGULAR));?>
```

To list letters before numbers, set the `orderby` flag to `SORT_NUMERIC` <sup>[Note 5](#info_5)</sup>

+ A
+ B
+ @
+ 1
+ 2

For example:

```php
<?php $alphabetise = alphabetise($page->children()->listed()->sortby('title'), array('key' => 'title', 'orderby'=>SORT_NUMERIC));?>
```

## Additional Notes

<sup id="info_1">1</sup> The `explode` function used for array parsing uses the tilde character `~` as the separator value. If this character appears in one of the `key` values, especially at the beginning of a string, you could run into sorting problems. You can manually change the separator value if required.

<sup id="info_2">2</sup> The value of the `orderby` flag is not a string.

<sup id="info_3">3</sup> PHP 8.2 [changed the way](https://php.watch/versions/8.2/ksort-SORT_REGULAR-order-changes) the `ksort` function behaves when using the `SORT_REGULAR` flag. In previous versions of PHP, the `SORT_REGULAR` flag listed numeric characters after alphabetical characters. It now arranges numeric characters before alphabetical characters, the same as the `SORT_STRING` and `SORT_NATURAL` flags.

<sup id="info_4">4</sup> This plugin uses the PHP `ksort` function, so other [sorting type parameters](https://www.php.net/manual/en/function.ksort.php) might work, but have not been tested.

<sup id="info_5">5</sup> Using the `SORT_NUMERIC` flag may result in unexpected results if any of your `key` values consist of single characters.


## Release Notes

### 0.1.3
+ Added check for duplicate `key` values.
+ Modified handling of single character `key` field text.
+ Changed plugin namespace from `shoesforindustry` to `splorp` to match fork.
+ Updated documentation.

### 0.1.2
+ Added field to `composer.json` for link in the Kirby Panel.

### 0.1.1
+ Additional fixes for Kirby 3 compatibility.

### 0.1.0
+ Renamed `alphabetise.php` to `index.php` for Kirby 3 compatibility.
+ Renamed `package.json` to `composer.json`
+ Updated documentation.

### 0.0.9
+ Added `orderby` key for alternative sort order.

### 0.0.8
+ Fixed `Array to string conversion` error.

### 0.0.7
+ Fixed a small bug introduced in the 0.0.6 update.

### 0.0.6
+ Fixed bug when using only a single character of text for a `key` field.
+ Updated documentation to remove workaround.

### 0.0.5
+ Discovered bug when using only a single character of text for a `key` field.
+ Updated documentation with explanation and possible workaround.

### 0.0.4
+ Bug fix for spaces in `explode` key, now `'~'` instead of space `' '`
+ Updated page code with a pre-sort `'sortby('title')'`
+ Updated documentation and examples.

### 0.0.3
+ Updated documentation.

### 0.0.2
+ Added error handling code.
+ Updated documentation.

### 0.0.1
+ Initial release.

## Authors

Russ Baldwin\
[shoesforindustry.net](https://shoesforindustry.net/)

Grant Hutchinson\
[splorp.com](https://splorp.com/)
