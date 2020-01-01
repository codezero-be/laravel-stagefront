<?php

namespace CodeZero\StageFront\Commands;

use CodeZero\DotEnvUpdater\DotEnvUpdater;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class SetCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stagefront:credentials {username?} {password?} {--encrypt : Encrypt the password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a username and password to login to the staging site.';

    /**
     * Execute the console command.
     *
     * @param \CodeZero\DotEnvUpdater\DotEnvUpdater $updater
     *
     * @return void
     */
    public function handle(DotEnvUpdater $updater)
    {
        $username = $this->argument('username') ?: $this->askUsername();
        $password = $this->argument('password') ?: $this->askPassword();
        $encrypt = $this->option('encrypt') ?: $this->askForEncryption();

        if ($encrypt) {
            $password = Hash::make($password);
        }

        $updater->set('STAGEFRONT_LOGIN', $username);
        $updater->set('STAGEFRONT_PASSWORD', $password);
        $updater->set('STAGEFRONT_ENCRYPT', $encrypt);

        $this->info("StageFront credentials were written to [.env].");
    }

    /**
     * Ask for a username.
     *
     * @return string
     */
    protected function askUsername()
    {
        $username = trim($this->ask('Choose a username:', 'stagefront'));

        if ($username) {
            return $username;
        }

        $this->error('Username can not be empty. Try again...');

        return $this->askUsername();
    }

    /**
     * Ask for a password and confirmation.
     *
     * @return string
     */
    protected function askPassword()
    {
        $password = $this->secret('Choose a password:');

        if (trim($password)) {
            return $this->askPasswordConfirmation($password);
        }

        $this->error('Password can not be empty. Try again...');

        return $this->askPassword();
    }

    /**
     * Ask for a password confirmation.
     *
     * @param string $password
     *
     * @return string
     */
    protected function askPasswordConfirmation($password)
    {
        $passwordConfirmation = $this->secret('Retype password:');

        if ($password === $passwordConfirmation) {
            return $password;
        }

        $this->error('Password did not match. Try again...');

        return $this->askPassword();
    }

    /**
     * Ask if the password should be encrypted.
     *
     * @return bool
     */
    protected function askForEncryption()
    {
        if ($this->argument('password')) {
            return false;
        }

        return $this->confirm('Encrypt password?');
    }
}
