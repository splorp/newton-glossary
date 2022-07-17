# Kirby Paperback Export

Export [Kirby](https://getkirby.com/) CMS content for use with the [Paperback Book Maker](https://ritsuko.chuma.org/paperback/).

## What does this plugin do?

The plugin generates a lightly formatted plain text file from a set of pages specified by the user. The text file is used to create a “book” package which can be viewed on a Newton OS device.

An example of the exported file can be downloaded using the link below. The file contains all of the terms currently published on the [Newton Glossary](https://newtonglossary.com/) site.

[newtonglossary.com/export/paperback](https://newtonglossary.com/export/paperback)

## What is a Paperback book, you ask?

Paperback is a simple cross-platform utility created by [David Fedor](https://web.archive.org/web/20171018055006/https://thefedors.com/pobox/) that takes plain text files and quickly packages them for viewing on a Newton OS device. Since the Paperback utility only runs under classic Mac OS and Windows, an online [Paperback Book Maker](https://ritsuko.chuma.org/paperback/) was developed by [Victor Rehorst](https://github.com/chuma) for all your cross-platform needs.

## Installation

After installing the plugin using one of the methods below, visiting `yoursite.com/export/paperback` should automatically download a text file without any additional configuration.

### Download

To install the plugin manually, [download the current release](https://github.com/splorp/kirby-paperback-export/releases), decompress the archive, and put the files in:

`site/plugins/paperback-export`

For [Kirby 2](https://github.com/getkirby-v2) sites, download version [1.0.1](https://github.com/splorp/kirby-paperback-export/releases/tag/1.0.1) of the plugin.

### Git Submodule

Installing the plugin as a Git submodule:

    $ cd your/project/root
    $ git submodule add https://github.com/splorp/kirby-paperback-export.git site/plugins/paperback-export
    $ git submodule update --init --recursive
    $ git commit -am "Add Kirby Paperback Export plugin"

Updating the plugin as a Git submodule:

    $ cd your/project/root
    $ git submodule foreach git checkout master
    $ git submodule foreach git pull
    $ git commit -am "Update submodules"
    $ git submodule update --init --recursive
    
## Options

### Table of contents prefix

Paperback books can include a table of contents created from the text found located in the `$page->title()` field. The following option can be added to the `site/config/config.php` file, allowing you to specify a custom table of contents prefix.

This option is not set by default.

```php
return [
	'splorp.paperback-export.tocPrefix' => '',
];
```

Specify a prefix.

```php
return [
	'splorp.paperback-export.tocPrefix' => ['@@TOC '],
];
```

### Include unlisted pages

By default, text located in the `$page->title()` and `$page->text()` fields for every page on your Kirby site will be included in the exported data. The following options can be added to the `site/config/config.php` file, allowing you to filter which pages are included based on certain criteria.

This option is set to true by default.

```php
return [
	'splorp.paperback-export.includeUnlisted' => true,
];
```

### Include Children Pages

Pages specified in this option will not be included, but the children of those pages will be included in the exported data.

This option is not set by default.

```php
return [
	'splorp.paperback-export.includeChildren' => [],
];
```

Specify one or more pages as an array.

```php
return [
	'splorp.paperback-export.includeChildren' => ['blog','newsletter'],
];
```

### Exclude Templates

Pages using the templates specified in this option will be excluded from the exported data.

This option is not set by default.

```php
return [
	'splorp.paperback-export.excludeTemplate' => [],
];
```

Specify one or more templates as an array.

```php
return [
	'splorp.paperback-export.excludeTemplate' => ['about','search'],
];
```

## Known Issues

+ Output is currently optimized for the [Newton Glossary](https://newtonglossary.com/) instance of [Kirby](https://getkirby.com/)

## Release Notes

### 2.0.3
+ Added option to specify the table of contents prefix
+ Added better exception checking for option values
+ Fixed malformed `support` field in `composer.json`
+ Removed extraneous comments from `index.php`

### 2.0.2
+ Added `keywords`, `homepage`, `support.docs`, `support.source` fields to `composer.json`

### 2.0.1.1
+ Fixed version number in `index.php`

### 2.0.1
+ Refactored replacement patterns in string functions
+ Removed extra line breaks introduced by heading elements
+ Better handling of pages containing `<img>` elements
+ Added the current date to the exported file

### 2.0.0
+ Refactored and updated for Kirby 3
+ Tweaked the option names to be more consistent and self explanatory
+ Moved the file export code into `snippets/export.php`
+ Renamed `snippets/page.php` to `snippets/content.php`

### 1.0.1
+ Refactored filtering options
+ Fixed formatting of paragraph breaks in `$page->text()`

### 1.0.0
+ Initial release

## Acknowledgements

A tip of the hat to [Pedro Borges](https://pedroborg.es/) and his [Kirby XML Sitemap](https://github.com/pedroborges/kirby-xml-sitemap) for providing the necessary framework and inspiration to attempt my own plugin.

## License

Copyright © 2017–2022 Grant Hutchinson

This project is licensed under the short and sweet [MIT License](https://opensource.org/licenses/MIT). This license allows you to do anything pretty much anything you want with the contents of the repository, as long as you provide proper attribution and don’t hold anyone liable.

See the [license.txt](https://raw.github.com/splorp/kirby-paperback-export/master/license.txt) file included in this repository for further details.

## Questions?

Contact me via [email](mailto:grant@splorp.com) or [Twitter](https://twitter.com/splorp).
