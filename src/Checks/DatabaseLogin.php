<?php

namespace CodeZero\StageFront\Checks;

use DB;
use Hash;

class DatabaseLogin implements Check
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
        $passwordField = config('stagefront.database_password_field');

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
        $table = config('stagefront.database_table');
        $loginField = config('stagefront.database_login_field');

        return DB::table($table)->where($loginField, '=', $login)->first();
    }
}
