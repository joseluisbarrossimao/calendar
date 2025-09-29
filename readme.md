# Calendar

## About Calendar

filesystem plugin for Calendar.

## Installation

* Download [Composer](https://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
* Run `php composer.phar require rest-full/Calendar` or composer installed globally `compser require rest-full/Calendar` or composer.json `"rest-full/Calendar": "1.0.0"` and install or update.

## Usage

The index:
 ```php
 <?php

require_once __DIR__ . "/src/path.php";

if (isset($_POST['method'])) {
    if ($_POST['method'] == 'event') {
        require_once __DIR__ . "/example/event.php";
    } else {
        require_once __DIR__ . "/example/example.php";
    }
} else {
    require_once __DIR__ . "/example/example.php";
}
```

## License

The csv is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
 
