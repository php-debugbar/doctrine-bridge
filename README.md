# Doctrine Bridge for PHP Debug Bar

## Demo

To run the demo, clone this repository and build the demo database.

```
cd demo && ./build.sh
```

Then start the Built-In PHP webserver from the root:

```
php -S localhost:8000
```

Then visit http://localhost:8000/demo/

## Testing

To test, run `php vendor/bin/phpunit`. 
To debug Browser tests, you can run `PANTHER_NO_HEADLESS=1 vendor/bin/phpunit --debug`. Run `vendor/bin/bdi detect drivers` to download the latest drivers.
