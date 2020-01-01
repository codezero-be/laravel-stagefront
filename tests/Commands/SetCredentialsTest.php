<?php

namespace CodeZero\StageFront\Tests\Commands;

class SetCredentialsTest extends CommandTestCase
{
    /** @test */
    public function it_sets_unencrypted_credentials()
    {
        $this->artisan('stagefront:credentials', ['username' => 'admin', 'password' => 'abc123'])
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertFalse($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function it_sets_encrypted_credentials_with_option()
    {
        $this->artisan('stagefront:credentials', ['username' => 'admin', 'password' => 'abc123', '--encrypt' => true])
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertNotEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertNotEmpty($this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertTrue($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function it_asks_for_credentials_and_does_not_encrypt_the_password()
    {
        $this->artisan('stagefront:credentials')
            ->expectsQuestion('Choose a username:', 'admin')
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->expectsQuestion('Encrypt password?', false)
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertFalse($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function it_asks_for_credentials_and_encrypts_the_password()
    {
        $this->artisan('stagefront:credentials')
            ->expectsQuestion('Choose a username:', 'admin')
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->expectsQuestion('Encrypt password?', true)
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertNotEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertNotEmpty($this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertTrue($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function username_can_not_be_empty()
    {
        $this->artisan('stagefront:credentials')
            ->expectsQuestion('Choose a username:', ' ')
            ->expectsOutput('Username can not be empty. Try again...')
            ->expectsQuestion('Choose a username:', 'admin')
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->expectsQuestion('Encrypt password?', false)
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertFalse($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function password_can_not_be_empty()
    {
        $this->artisan('stagefront:credentials')
            ->expectsQuestion('Choose a username:', 'admin')
            ->expectsQuestion('Choose a password:', ' ')
            ->expectsOutput('Password can not be empty. Try again...')
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->expectsQuestion('Encrypt password?', false)
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertFalse($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function password_should_be_retyped_correctly()
    {
        $this->artisan('stagefront:credentials')
            ->expectsQuestion('Choose a username:', 'admin')
            ->expectsQuestion('Choose a password:', 'test')
            ->expectsQuestion('Retype password:', 'oops-a-typo')
            ->expectsOutput('Password did not match. Try again...')
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->expectsQuestion('Encrypt password?', false)
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertFalse($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function it_asks_for_a_password_only_if_you_already_specified_a_username()
    {
        $this->artisan('stagefront:credentials', ['username' => 'admin'])
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->expectsQuestion('Encrypt password?', false)
            ->assertExitCode(0);

        $this->assertEquals('admin', $this->updater->get('STAGEFRONT_LOGIN'));
        $this->assertEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertFalse($this->updater->get('STAGEFRONT_ENCRYPT'));
    }

    /** @test */
    public function it_does_not_ask_for_encryption_if_you_already_typed_the_option()
    {
        $this->artisan('stagefront:credentials', ['--encrypt' => true])
            ->expectsQuestion('Choose a username:', 'admin')
            ->expectsQuestion('Choose a password:', 'abc123')
            ->expectsQuestion('Retype password:', 'abc123')
            ->assertExitCode(0);

        $this->assertNotEquals('abc123', $this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertNotEmpty($this->updater->get('STAGEFRONT_PASSWORD'));
        $this->assertTrue($this->updater->get('STAGEFRONT_ENCRYPT'));
    }
}
