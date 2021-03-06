<?php

namespace CodeZero\StageFront\Authenticators;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseAuthenticator implements Authenticator
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
        $user = $this->findUser($login);

        return $user !== null && $this->checkPassword($password, $user);
    }

    /**
     * Check if the given password is valid.
     *
     * @param string $password
     * @param \stdClass $user
     *
     * @return bool
     */
    protected function checkPassword($password, $user)
    {
        $passwordField = Config::get('stagefront.database_password_field');

        return Hash::check($password, $user->{$passwordField});
    }

    /**
     * Find the given login in the database.
     *
     * @param string $login
     *
     * @return \stdClass|null
     */
    protected function findUser($login)
    {
        if ( ! $this->loginIsAllowed($login)) {
            return null;
        }

        $table = Config::get('stagefront.database_table');
        $loginField = Config::get('stagefront.database_login_field');

        return DB::table($table)->where($loginField, '=', $login)->first();
    }

    /**
     * Check if the given login is allowed.
     *
     * @param string $login
     *
     * @return bool
     */
    protected function loginIsAllowed($login)
    {
        $logins = $this->getWhitelist();

        return count($logins) === 0 || in_array($login, $logins);
    }

    /**
     * Get the logins that are whitelisted.
     *
     * @return array
     */
    protected function getWhitelist()
    {
        $whitelist = Config::get('stagefront.database_whitelist', []);

        if ( ! is_array($whitelist)) {
            $whitelist = explode(',', $whitelist) ?: [];
        }

        $logins = array_map(function ($login) {
            return trim($login);
        }, $whitelist);

        return array_filter($logins);
    }
}
