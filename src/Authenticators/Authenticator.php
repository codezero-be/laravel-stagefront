<?php

namespace CodeZero\StageFront\Authenticators;

interface Authenticator
{
    /**
     * Check if the given credentials are valid.
     *
     * @param string $login
     * @param string $password
     *
     * @return bool
     */
    public function check($login, $password);
}
