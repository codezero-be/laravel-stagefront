<?php

namespace CodeZero\StageFront\Tests\Feature;

use CodeZero\StageFront\Tests\Stubs\User;
use CodeZero\StageFront\Tests\TestCase;
use Route;

class StageFrontTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->url = config('stagefront.url');

        Route::get('/page', function () {
            return 'Some Page';
        })->middleware(config('stagefront.middleware'));
    }

    /** @test */
    public function it_redirects_to_a_login_screen_when_stagefront_is_enabled()
    {
        config()->set('stagefront.enabled', true);

        $this->get('/page')->assertRedirect($this->url);
    }

    /** @test */
    public function it_does_not_redirect_to_a_login_screen_when_stagefront_is_disabled()
    {
        config()->set('stagefront.enabled', false);

        $this->get('/page')->assertStatus(200)->assertSee('Some Page');
    }

    /** @test */
    public function it_redirects_to_the_intended_url_when_you_provide_valid_credentials()
    {
        config()->set('stagefront.enabled', true);
        config()->set('stagefront.login', 'tester');
        config()->set('stagefront.password', 'p4ssw0rd');

        $this->setIntendedUrl('/page');

        $response = $this->submitForm([
            'login' => 'tester',
            'password' => 'p4ssw0rd',
        ]);

        $response->assertRedirect('/page');
    }

    /** @test */
    public function it_does_not_allow_access_when_you_provide_invalid_credentials()
    {
        config()->set('stagefront.enabled', true);
        config()->set('stagefront.login', 'tester');
        config()->set('stagefront.password', 'p4ssw0rd');

        $this->setIntendedUrl('/page');

        $response = $this->submitForm([
            'login' => 'tester',
            'password' => 'faulty',
        ]);

        $response->assertRedirect($this->url)
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function the_password_may_be_stored_encrypted()
    {
        config()->set('stagefront.enabled', true);
        config()->set('stagefront.login', 'tester');
        config()->set('stagefront.password', bcrypt('p4ssw0rd'));
        config()->set('stagefront.encrypted', true);

        $this->setIntendedUrl('/page');

        $response = $this->submitForm([
            'login' => 'tester',
            'password' => 'p4ssw0rd',
        ]);

        $response->assertRedirect('/page');
    }

    /** @test */
    public function the_users_in_the_database_can_be_used_for_logging_in()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.io',
            'password' => bcrypt('str0ng p4ssw0rd'),
        ]);

        config()->set('stagefront.enabled', true);
        config()->set('stagefront.database', true);
        config()->set('stagefront.database_table', 'users');
        config()->set('stagefront.database_login_field', 'email');
        config()->set('stagefront.database_password_field', 'password');

        $this->setIntendedUrl('/page');

        $response = $this->submitForm([
            'login' => 'john@doe.io',
            'password' => 'str0ng p4ssw0rd',
        ]);

        $response->assertRedirect('/page');
    }

    /** @test */
    public function you_can_limit_which_database_users_have_access()
    {
        $this->loadLaravelMigrations(['--database' => 'testing']);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.io',
            'password' => bcrypt('str0ng p4ssw0rd'),
        ]);
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@doe.io',
            'password' => bcrypt('str0ng p4ssw0rd'),
        ]);
        User::create([
            'name' => 'Mr. Smith',
            'email' => 'mr@smith.io',
            'password' => bcrypt('str0ng p4ssw0rd'),
        ]);

        config()->set('stagefront.enabled', true);
        config()->set('stagefront.database', true);
        config()->set('stagefront.database_whitelist', 'john@doe.io,jane@doe.io');
        config()->set('stagefront.database_table', 'users');
        config()->set('stagefront.database_login_field', 'email');
        config()->set('stagefront.database_password_field', 'password');

        $this->setIntendedUrl('/page')->submitForm([
            'login' => 'john@doe.io',
            'password' => 'str0ng p4ssw0rd',
        ])->assertRedirect('/page');

        $this->setIntendedUrl('/page')->submitForm([
            'login' => 'jane@doe.io',
            'password' => 'str0ng p4ssw0rd',
        ])->assertRedirect('/page');

        $this->setIntendedUrl('/page')->submitForm([
            'login' => 'mr@smith.io',
            'password' => 'str0ng p4ssw0rd',
        ])->assertRedirect($this->url)->assertSessionHasErrors('password');
    }

    /**
     * Tell Laravel we navigated to this intended URL and
     * got redirected to the login page so that
     * redirect()->intended() will work.
     *
     * @param string $url
     *
     * @return $this
     */
    protected function setIntendedUrl($url)
    {
        session()->put('url.intended', $url);

        return $this;
    }

    /**
     * Send a post request.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function submitForm(array $credentials)
    {
        $response = $this->post($this->url, $credentials, [
            // Since we're calling routes directly,
            // we need to fake the referring page
            // so that redirect()->back() will work.
            'HTTP_REFERER' => $this->url
        ]);

        return $response;
    }
}
