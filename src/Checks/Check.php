<?php

namespace CodeZero\StageFront\Checks;

interface Check
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
