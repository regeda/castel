# Castel

Castel is a fast Dependency Injection Container. The class for PHP 5.3 consists of just one file.

[![Build Status](https://travis-ci.org/regeda/castel.png?branch=master)](https://travis-ci.org/regeda/castel)

## Installing

Pick up the `src/Castel.php` file or install it with [Composer](https://getcomposer.org/) :

```json
{
    "require": {
        "regeda/castel": "0.1.*"
    }
}
```

Creating a container is a matter of instating the Castel class

```php
$container = new Castel();
```

## Defining parameters

```php
$container->share('foo', 'bar');
$container->share('something', new Something());
```

Retrieving parameters as plain properties:

```php
$container->foo; // bar
$container->something; // instance of Something class
```

## Defining services

Services are defined by anonymous functions that return an instance of an object

```php
$container->share('session', function () {
    return new Session();
});
```

Using the defined services

```php
$session = $container->session; // instance of Session class
```

## Extending services after creation

```php
$container->share('mail', function () {
    return new \Zend_Mail();
});
$container->extend('mail', function ($mail, $container) {
    $mail->setFrom($container->getValue('mail.default_from'));
    return $mail;
});
```

Source: https://github.com/regeda/castel