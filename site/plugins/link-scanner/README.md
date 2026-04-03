# Link Scanner for Kirby

![Plugin Preview](src/assets/kirby-link-scanner-plugin.jpg)

Adds a panel area with the ability to scan your site's content files for broken links. The plugin can traverse internal Kirby UUID links and external links in various formats reporting back those that return 404 or 500-level errors with a quick means to go to the source page and update or remove them.

## Installation

### [Kirby CLI](https://github.com/getkirby/cli)
    
```bash
kirby plugin:install scottboms/kirby-link-scanner
```

### Git submodule

```bash
git submodule add https://github.com/scottboms/kirby-link-scanner.git site/plugins/link-scanner
```

### Copy and Paste

1. [Download](https://github.com/scottboms/kirby-link-scanner/archive/master.zip) the contents of this repository as Zip file.
2. Rename the extracted folder to `link-scanner` and copy it into the `site/plugins/` directory in your Kirby project.


## Configuration Options

| Property                         | Default              | Req? | Description                       |
|----------------------------------|----------------------|------|-----------------------------------|
| scottboms.link-scanner.timeout   | `8`                  | No   | Timeout for the scanner process   |
| scottboms.link-scanner.userAgent | `Kirby Link Scanner` | No   | Set a custom UserAgent string     |

Example Config:

```php
<?php
  return [
	  'scottboms.link-scanner' => [
      'timeout'    => 10,
      'userAgent'  => 'Kirby Link Scanner',
    ]
  ]
```


## Compatibility

* Kirby 5.x
* PHP 8.3+ with cURL and php cli exec()


## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test before using it in a production environment. If you identify an issue, typo, etc, please [create a new issue](/issues/new) so I can investigate.


## License

[MIT](https://opensource.org/licenses/MIT)
