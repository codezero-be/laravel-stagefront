# Laravel StageFront

[![GitHub release](https://img.shields.io/github/release/codezero-be/laravel-stagefront.svg)]()
[![License](https://img.shields.io/packagist/l/codezero/laravel-stagefront.svg)]()
[![Build Status](https://scrutinizer-ci.com/g/codezero-be/laravel-stagefront/badges/build.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-stagefront/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/codezero-be/laravel-stagefront/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-stagefront/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/codezero-be/laravel-stagefront/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/codezero-be/laravel-stagefront/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/codezero/laravel-stagefront.svg)](https://packagist.org/packages/codezero/laravel-stagefront)

#### Quickly add some password protection to a staging site.

## Requirements

-   PHP >= 7.0
-   Laravel >= 5.5

## Installation

Require the package via Composer:

```
composer require codezero/laravel-stagefront
```

Add the middleware to the web middleware group, **right after the `SartSession` middleware**:

```php
\CodeZero\StageFront\Middleware\RedirectIfStageFrontIsEnabled::class,
```

Laravel will automatically register the `ServiceProvider`.

## Quick Setup

Set some options in your `.env` file or publish the [configuration file](#publish-configuration-file).

Enable StageFront and choose a login and password:

| Option                 | Type     | Default      |
| ---------------------- | -------- | ------------ |
| `STAGEFRONT_ENABLED`   | `bool`   | `false`      |
| `STAGEFRONT_LOGIN`     | `string` | `stagefront` |
| `STAGEFRONT_PASSWORD`  | `string` | `stagefront` |
| `STAGEFRONT_ENCRYPTED` | `bool`   | `false`      |

By default StageFront is disabled and uses a plain text password.

If you set `STAGEFRONT_ENCRYPTED` to `true` the password should be a hashed value.
You can generate this using Laravel's `bcrypt('your password')` function.

## Database Logins

If you have existing users in the database and want to use those credentials, you can set `STAGEFRONT_DATABASE` to `true`. The above settings will then be ignored.

| Option                               | Type            | Default    |
| ------------------------------------ | --------------- | ---------- |
| `STAGEFRONT_DATABASE`                | `bool`          | `false`    |
| `STAGEFRONT_DATABASE_WHITELIST`      | `string`|`null` | `null`     |
| `STAGEFRONT_DATABASE_TABLE`          | `string`        | `users`    |
| `STAGEFRONT_DATABASE_LOGIN_FIELD`    | `string`        | `email`    |
| `STAGEFRONT_DATABASE_PASSWORD_FIELD` | `string`        | `password` |

If you want to grant access to just a few of those users, you can whitelist them by setting `STAGEFRONT_DATABASE_WHITELIST` to a comma separated string: `'john@doe.io,jane@doe.io'`.

By default the `users` table is used with the `email` and `password` field names. But you can change this if you are using some other table or fields.

## Change Route URL

By default a `GET` and `POST` route will be registered with the `/stagefront` URL.
You can change the URL by setting this option:

| Option           | Type     | Default      |
| ---------------- | -------- | ------------ |
| `STAGEFRONT_URL` | `string` | `stagefront` |

It runs under the `web` middleware since it uses the session to keep you logged in. You can change the middleware if needed in the [configuration file](#publish-configuration-file).

## Ignore URLs

If for any reason you wish to disable StageFront on specific routes, you can add these to the `ignore_urls` array in the [configuration file](#publish-configuration-file). You can use wildcards if needed. You can't set this in the `.env` file.

For example:

```php
'ignore_urls' => [
    // ignores /john, but noting under /john
    '/john',
    // ignores everyting under /jane, but not /jane itself
    '/jane/*',
],
```

## Link Live Site

If you set the URL to your live site, a link will be shown underneath the login form.

| Option                 | Type     | Default |
| ---------------------- | -------- | ------- |
| `STAGEFRONT_LIVE_SITE` | `string` | `null`  |

Make sure you enter the full URL, including `https://`.

## Publish Configuration File

You can also publish the configuration file.

```
php artisan vendor:publish
```

Each option is documented.

## Translations & Login View

You can publish the translations to quickly adjust the text on the login screen and the errors. If you want to customize the login page entirely, you can also publish the view.

```
php artisan vendor:publish
```

Extra translations are always welcome. :)

## Laravel Debugbar

Laravel Debugbar will be disabled on the StageFront login page automatically if you use it in your project. This will hide any potential sensitive data from the public, if by accident Debugbar is running on your staging site.

## Testing

```
vendor/bin/phpunit
```

## Security

If you discover any security related issues, please [e-mail me](mailto:ivan@codezero.be) instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
