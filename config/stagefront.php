<?php

return [

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
     * or use bcrypt('my-secret') to generate an encrypted value.
     *
     * Default: 'stagefront' (change this!)
     */
    'password' => env('STAGEFRONT_PASSWORD', 'stagefront'),

    /**
     * Are you using an encrypted password?
     * Set this to true and use bcrypt('my-secret') to generate an encrypted value.
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
     * Enter a string of comma separated logins, or null to allow all users.
     * For example: 'john@doe.io,jane@doe.io'
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
     * The URL to use for the StageFront login route.
     * This URL will be used for a GET and POST request.
     *
     * Default: 'stagefront' (= site.com/stagefront)
     */
    'url' => env('STAGEFRONT_URL', 'stagefront'),

    /**
     * The route middleware to use.
     * Since StageFront uses the session, we definitely require
     * the web middleware group. But you can change it if needed.
     *
     * Default: 'web'
     */
    'middleware' => env('STAGEFRONT_MIDDLEWARE', 'web'),

];
