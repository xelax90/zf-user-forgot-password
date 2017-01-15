# User Forgot Password module

This module provides a simple password recovery system based on `xelax90/zf-user-notificaiton`

## Installation

Installation of XelaxUserForgotPassword uses composer. For composer documentation, 
please refer to [getcomposer.org](http://getcomposer.org/).

```sh
composer require xelax90/zf-user-forgot-password
```

Then add `XelaxUserForgotPassword` to your `config/application.config.php` and run 
the doctrine schema update to create the database table:

```sh
php vendor/bin/doctrine-module orm:schema-tool:update --force 
```

Now copy the provided configuration files
`vendor/xelax90/zf-user-forgot-password/config/xelax-user-forgot-password.global.php` and
`vendor/xelax90/zf-user-forgot-password/config/xelax-user-forgot-password.local.php.dist` 
into your `config/autoload` directory. Also make another copy of the 
`xelax-user-forgot-password.local.php.dist` file without the `.dist` extension.

## Configuration

### E-Mail Templates

You can overwrite the localized e-mail templates by providing a viewScript for 
xelax-user-forgot-password/email/$LANGUAGE/$NOTIFICATION/$TEMPLATE. You can find the 
pre-defined ones in the view folder.

### Request Lifetime

You can configure the request lifetime in the global configuration with the 
```request_lifetime``` key. The passed value must either be a DateInterval object or
an interval specification for its constructor 
(see [DateInterval::__construct](http://php.net/manual/de/dateinterval.construct.php)).
It defaults to one day.

