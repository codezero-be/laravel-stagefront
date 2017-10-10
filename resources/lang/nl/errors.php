<?php

return [

    'login' => [
        'required' => 'Vul een login in.',
    ],

    'password' => [
        'required' => 'Vul een wachtwoord in.',
        'match' => 'Ongeldige gebruikersnaam of wachtwoord.',
    ],

    'throttled' => [
        'intro' => 'Maximum aantal mislukte login pogingen overschreden.',
        'remaining' => 'Probeer opnieuw over :remaining.',
        'moment' => 'enkele ogenblikken',
        'back' => 'Terug',
    ],

];
