# Doctrine Bridge for PHP Debug Bar

## Install

This requires https://github.com/php-debugbar/php-debugbar/ to be installed first.


```
composer require php-debugbar/doctrine-bridge:"^2@beta"
```

## Demo

To run the demo, clone this repository and build the demo database.

```
composer run install-demo
```

Then start the Built-In PHP webserver from the root:

```
composer run demo
```

Then visit http://localhost:8000/demo/

## Testing

To test, run `php vendor/bin/phpunit`.
To debug Browser tests, you can run `PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --debug`. Run `vendor/bin/bdi detect drivers` to download the latest drivers.
