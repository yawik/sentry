YAWIK Sentry Integration
========

This integrates [Sentry](https://sentry.io) error reporting into an YAWIK instance


Requirements
------------

running [YAWIK](https://github.com/cross-solution/YAWIK)


Installation
------------

Require a dependency via composer.

```bash
composer require yawik/sentry
```

Enable the module for the Zend module manager via creating the `sentry.module.php` file in the `/config/autoload` directory with the following content.

```php
<?php
return [
    'YkSentry'
];
```

Configuration
-------------

TBD


Development
-------
1.  Clone project
```sh
$ git clone git@github.com:yawik/sentry.git /path/to/yawik-sentry
```

2. Install dependencies:
```sh
$ composer install
```

3. Run PHPUnit Tests
```sh
$ ./vendor/bin/phpunit
```

Licence
-------

MIT
