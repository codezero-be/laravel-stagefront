<?php

return [

    /**
     * The app name that is shown on the login and throttle page.
     *
     * Default: config('app.name')
     */
    'app_name' => env('STAGEFRONT_APP_NAME', config('app.name')),

    /**
     * Set an optional URL of the live version of the website.
     * This will be displayed on the login screen if set.
     * For example: 'https://www.site.com'
     *
     * Default: null
     */
    'live_site' => env('STAGEFRONT_LIVE_SITE', null),

    /**
     * Enable StageFront to prevent anyone from accessing the website
     * without first entering a login and password.
     *
     * Default: false
     */
    'enabled' => env('STAGEFRONT_ENABLED', false),

    /**
     * The login to use.
     * This can be a username or an e-mail address.
     *
     * Default: 'stagefront' (change this!)
     */
    'login' => env('STAGEFRONT_LOGIN', 'stagefront'),

    /**
     * The password to use. You can set it in plain text,
     * or use \Hash::make('my-secret') to generate an encrypted value.
     *
     * Default: 'stagefront' (change this!)
     */
    'password' => env('STAGEFRONT_PASSWORD', 'stagefront'),

    /**
     * Are you using an encrypted password?
     * Set this to true and use \Hash::make('my-secret') to generate an encrypted value.
     * Or set this to false and use a plain text password.
     *
     * Default: false
     */
    'encrypted' => env('STAGEFRONT_ENCRYPTED', false),

    /**
     * If you have existing users in the database, you can use these settings
     * to log them in via the StageFront login page.
     * This will not log them in into your app/website.
     * When using the database, the other login options will be irrelevant.
     *
     * Default: false
     */
    'database' => env('STAGEFRONT_DATABASE', false),

    /**
     * When using the database, you can limit access to specific users.
     * Enter aan array or string of comma separated logins, or null to allow all users.
     * For example: ['john@doe.io', 'jane@doe.io'] or 'john@doe.io,jane@doe.io'
     *
     * Default: null
     */
    'database_whitelist' => env('STAGEFRONT_DATABASE_WHITELIST', null),

    /**
     * When using the database, you can configure the table and fields to use.
     */
    'database_table' => env('STAGEFRONT_DATABASE_TABLE', 'users'),
    'database_login_field' => env('STAGEFRONT_DATABASE_LOGIN_FIELD', 'email'),
    'database_password_field' => env('STAGEFRONT_DATABASE_PASSWORD_FIELD', 'password'),

    /**
     * Allow access from whitelisted IP's.
     * Enter an array or string of comma separated IP's or null to allow all IP's.
     * For example: ['1.2.3.4', '1.2.3.4'] or '1.2.3.4,1.2.3.4'
     * If empty, all IP's are allowed.
     *
     * Default: null
     */
    'ip_whitelist' => env('STAGEFRONT_IP_WHITELIST', null),

    /**
     * Should only whitelisted IP's get access?
     * If set to false, non whitelisted IP's will be presented with the login form.
     * If set to true, non whitelisted IP's will get a 403 Forbidden error.
     *
     * Default: false
     */
    'ip_whitelist_only' => env('STAGEFRONT_IP_WHITELIST_ONLY', false),

    /**
     * Require users with whitelisted IP's to also login.
     *
     * Default: false
     */
    'ip_whitelist_require_login' => env('STAGEFRONT_IP_WHITELIST_REQUIRE_LOGIN', false),

    /**
     * The URL to use for the StageFront login route.
     * This URL will be used for a GET and POST request.
     *
     * Default: 'stagefront' (= site.com/stagefront)
     */
    'url' => env('STAGEFRONT_URL', 'stagefront'),

    /**
     * To prevent malicious users from brute forcing passwords
     * login attempts will be throttled unless you disable it.
     *
     * Default: true
     */
    'throttle' => env('STAGEFRONT_THROTTLE', true),

    /**
     * Number of failed login attempts per minute before
     * users are locked out for a period of time.
     *
     * Default: 3
     */
    'throttle_tries' => env('STAGEFRONT_THROTTLE_TRIES', 3),

    /**
     * Number of minutes to lock out users after reaching
     * the maximum number of login attempts.
     *
     * Default: 5
     */
    'throttle_delay' => env('STAGEFRONT_THROTTLE_DELAY', 5),

    /**
     * The route middleware to use.
     * Since StageFront uses the session, we definitely require
     * the web middleware group. But you can change it if needed.
     *
     * Default: ['web', \CodeZero\StageFront\Middleware\DisableLaravelDebugbar::class]
     */
    'middleware' => [
        'web',
        \CodeZero\StageFront\Middleware\DisableLaravelDebugbar::class,
    ],

    /**
     * The following URLs will be ignored by StageFront.
     * Access to these URLs will never be blocked.
     * You can use wildcards:
     * '/route/*' (this does not include '/route')
     *
     * Default: []
     */
    'ignore_urls' => [],

];
