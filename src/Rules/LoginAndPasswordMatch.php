<?php

namespace CodeZero\StageFront\Rules;

use Hash;
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
        return $this->verifyCredentials($this->login, $password);
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

    /**
     * Verify that a login and password are valid.
     *
     * @param string $login
     * @param string $password
     *
     * @return bool
     */
    protected function verifyCredentials($login, $password)
    {
        return $this->verifyLogin($login) && $this->verifyPassword($password);
    }

    /**
     * Verify that the given login is valid.
     *
     * @param string $login
     *
     * @return bool
     */
    protected function verifyLogin($login)
    {
        return $login === config('stagefront.login');
    }

    /**
     * Verify that the given password is valid.
     *
     * @param string $password
     *
     * @return bool
     */
    protected function verifyPassword($password)
    {
        $validPassword = config('stagefront.password');
        $encrypted = config('stagefront.encrypted');

        if ($encrypted === true) {
            return Hash::check($password, $validPassword);
        }

        return $password === $validPassword;
    }
}
