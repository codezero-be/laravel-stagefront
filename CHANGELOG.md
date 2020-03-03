# Changelog

All Notable changes to `StageFront` will be documented in this file.

## 2.2.0 (2020-03-03)

- Add support for Laravel 7
- Add test config for PHP 7.4

## 2.1.0 (2020-01-03)

- Add ability to whitelist IP's

## 2.0.1 (2020-01-01)

- Fix incorrect env key

## 2.0.0 (2020-01-01)

- Drop support for PHP 7.0
- Drop support for Laravel 5.5
- Drop support for Laravel 5.6
- Add command: `php artisan stagefront:credentials`
- Add command: `php artisan stagefront:enable`
- Add command: `php artisan stagefront:disable`

## 1.1.4 (2020-01-01)

- Fix typo in README

## 1.1.3 (2017-10-11)

-   Make app name an option, defaults to `config('app.name')`

## 1.1.2 (2017-10-10)

-   Set intended URL before logging in

## 1.1.1 (2017-10-10)

-   Don't autoload middleware due to session issues

## 1.1.0 (2017-10-10)

-   Add login throttling
-   Autoload middleware
-   Disable Laravel Debugbar on all StageFront routes

## 1.0.0 (2017-10-10)

- Version 1.0.0 of `StageFront`
