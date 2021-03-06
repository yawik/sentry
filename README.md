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

Copy the file `config/sentry.module-options.local.php.dist` into the `config/autoload` directory and strip off the `.dist` extension.

Adjust the configuration.   

* `$enable`:   
  You may disable sending events to Sentry. (e.g. for testing or development environments.)
* `$sentryConfig`:  
  You can use any configuration keys that can be used in the function `\Sentry\init()` as [described here](https://docs.sentry.io/error-reporting/configuration/?platform=php).   
  However, the `dsn` key is required, and sending events will be disabled, if it is not set or empty - regardless of the `$enabled` setting above.

```php
$enable = true;

$sentryConfig = [
    // the DSN (public key) for the sentry server
    'dsn' => '',
];
```

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
