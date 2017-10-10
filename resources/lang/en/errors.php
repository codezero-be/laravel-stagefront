<?php

return [

    'login' => [
        'required' => 'Enter a login.',
    ],

    'password' => [
        'required' => 'Enter a password.',
        'match' => 'Invalid credentials.',
    ],

    'throttled' => [
        'intro' => 'Maximum number of failed login attempts exceeded.',
        'remaining' => 'Try again in :remaining.',
        'moment' => 'a moment',
        'back' => 'Back',
    ],

];
