<?php

namespace CodeZero\StageFront\Authenticators;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;

class EncryptedAuthenticator implements Authenticator
{
    /**
     * Check if the given credentials are valid.
     *
     * @param string $login
     * @param string $password
     *
     * @return bool
     */
    public function check($login, $password)
    {
        return $this->checkLogin($login) && $this->checkPassword($password);
    }

    /**
     * Check if the given login is valid.
     *
     * @param string $login
     *
     * @return bool
     */
    protected function checkLogin($login)
    {
        return $login === Config::get('stagefront.login');
    }

    /**
     * Check if the given password is valid.
     *
     * @param string $password
     *
     * @return bool
     */
    protected function checkPassword($password)
    {
        return Hash::check($password, Config::get('stagefront.password'));
    }
}
