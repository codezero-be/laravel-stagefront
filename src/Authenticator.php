<?php

namespace CodeZero\StageFront;

use CodeZero\StageFront\Authenticators\DatabaseAuthenticator;
use CodeZero\StageFront\Authenticators\EncryptedAuthenticator;
use CodeZero\StageFront\Authenticators\PlainTextAuthenticator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class Authenticator
{
    /**
     * Use an applicable check to verify that
     * the given credentials are valid.
     *
     * @param string $login
     * @param string $password
     *
     * @return bool
     */
    public static function checkCredentials($login, $password)
    {
        $checkers = [
            DatabaseAuthenticator::class => Config::get('stagefront.database') === true,
            EncryptedAuthenticator::class => Config::get('stagefront.encrypted') === true,
            PlainTextAuthenticator::class => true, //=> Default
        ];

        foreach ($checkers as $checker => $applicable) {
            if ($applicable === true) {
                return App::make($checker)->check($login, $password);
            }
        }
    }
}
