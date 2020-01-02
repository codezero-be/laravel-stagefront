<?php

namespace CodeZero\StageFront\Rules;

use CodeZero\StageFront\Authenticator;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Request;

class LoginAndPasswordMatch implements Rule
{
    /**
     * Login
     *
     * @var string
     */
    protected $login;

    /**
     * Create a new rule instance.
     *
     * @param string $loginField
     */
    public function __construct($loginField = 'login')
    {
        $this->login = Request::get($loginField);
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $passwordField
     * @param string $password
     *
     * @return bool
     */
    public function passes($passwordField, $password)
    {
        return Authenticator::checkCredentials($this->login, $password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return Lang::get('stagefront::errors.password.match');
    }
}
