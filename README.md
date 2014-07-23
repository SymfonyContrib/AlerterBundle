**This code is part of the [SymfonyContrib](http://symfonycontrib.com/) community.**

# Symfony2 AlerterBundle

###Features:

* Create alerts to fire when data points pass tests.
* Uses CronBundle for scheduled monitoring.
* Uses Monolog, or any PSR-3 logger, to send alerts.

## Installation

### 1. Add the bundle to your composer.json

```
"require": {
    "symfonycontrib/alerter-bundle": "@stable"
}
```

### 2. Install the bundle using composer

```bash
$ composer update symfonycontrib/alerter-bundle
```

### 3. Add this bundle to your application's kernel:

```php
    new SymfonyContrib\Bundle\CronBundle\CronBundle(),
    new SymfonyContrib\Bundle\AlerterBundle\AlerterBundle(),
```

### 4. Add routing for admin interface:



## Usage Examples

@todo
