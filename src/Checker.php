<?php

namespace CodeZero\StageFront;

use CodeZero\StageFront\Checks\DatabaseLogin;
use CodeZero\StageFront\Checks\EncryptedLogin;
use CodeZero\StageFront\Checks\PlainTextLogin;

class Checker
{
    /**
     * Use an applicable checker to verify that
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
            DatabaseLogin::class => config('stagefront.database') === true,
            EncryptedLogin::class => config('stagefront.encrypted') === true,
            PlainTextLogin::class => true, //=> Default
        ];

        foreach ($checkers as $checker => $applicable) {
            if ($applicable === true) {
                return app($checker)->check($login, $password);
            }
        }
    }
}
