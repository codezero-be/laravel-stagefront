<?php

namespace CodeZero\StageFront\Rules;

use CodeZero\StageFront\Checker;
use Illuminate\Contracts\Validation\Rule;

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
        $this->login = request($loginField);
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
        return Checker::checkCredentials($this->login, $password);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('stagefront::errors.password.match');
    }
}
