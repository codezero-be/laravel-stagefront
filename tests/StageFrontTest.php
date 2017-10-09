<?php

namespace CodeZero\StageFront\Tests;

use CodeZero\StageFront\Middleware\RedirectIfStageFrontIsEnabled;
use CodeZero\StageFront\Tests\Stubs\User;
use Illuminate\Contracts\Http\Kernel;
use Route;

class StageFrontTest extends TestCase
{
    /**
     * StageFront URL
     *
     * @var string
     */
    protected $url;

    public function setUp()
    {
        parent::setUp();

        // Note that changing the URL or middleware config in the tests
        // has no effect until you call $this->enableStageFront().
        // It is disabled by default so nothing is loaded by default.
        $this->url = config('stagefront.url');

        $this->registerRoute('/page', 'Some Page');
    }

    /** @test */
    public function it_redirects_to_a_login_screen_when_stagefront_is_enabled()
    {
        $this->enableStageFront();

        $this->get('/page')->assertRedirect($this->url);
    }

    /** @test */
    public function it_does_not_redirect_to_a_login_screen_when_stagefront_is_disabled()
    {
        $this->get('/page')->assertStatus(200)->assertSee('Some Page');
    }

    /** @test */
    public function the_login_route_does_not_exist_when_stagefront_is_disabled()
    {
        $this->get($this->url)->assertStatus(404);
    }

    /** @test */
    public function it_redirects_to_the_intended_url_when_you_provide_valid_credentials()
    {
        config()->set('stagefront.login', 'tester');
        config()->set('stagefront.password', 'p4ssw0rd');

        $this->enableStageFront();
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
        config()->set('stagefront.login', 'tester');
        config()->set('stagefront.password', 'p4ssw0rd');

        $this->enableStageFront();
        $this->setIntendedUrl('/page');

        $response = $this->submitForm([
            'login' => 'tester',
            'password' => 'faulty',
        ]);

        $response->assertRedirect($this->url)
            ->assertSessionHasErrors('password');
    }

    /** @test */
    public function it_redirects_home_if_you_are_already_logged_in()
    {
        $this->enableStageFront();

        session()->put('stagefront.unlocked', true);

        $this->get($this->url)->assertRedirect('/');
    }

    /** @test */
    public function the_password_may_be_stored_encrypted()
    {
        config()->set('stagefront.login', 'tester');
        config()->set('stagefront.password', bcrypt('p4ssw0rd'));
        config()->set('stagefront.encrypted', true);

        $this->enableStageFront();
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

        config()->set('stagefront.database', true);
        config()->set('stagefront.database_table', 'users');
        config()->set('stagefront.database_login_field', 'email');
        config()->set('stagefront.database_password_field', 'password');

        $this->enableStageFront();
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

        config()->set('stagefront.database', true);
        config()->set('stagefront.database_whitelist', 'john@doe.io,jane@doe.io');
        config()->set('stagefront.database_table', 'users');
        config()->set('stagefront.database_login_field', 'email');
        config()->set('stagefront.database_password_field', 'password');

        $this->enableStageFront();

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

    /** @test */
    public function urls_can_be_ignored_so_access_is_not_denied_by_stagefront()
    {
        $this->registerRoute('/public', 'Public');
        $this->registerRoute('/public/route', 'Route');

        config()->set('stagefront.ignore_urls', ['/public/*']);

        $this->enableStageFront();

        $this->get('/public')->assertRedirect($this->url);
        $this->get('/public/route')->assertStatus(200)->assertSee('Route');
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

    /**
     * Enable StageFront.
     * Routes and middleware haven't been loaded, so we should do this now.
     *
     * @return void
     */
    protected function enableStageFront()
    {
        config()->set('stagefront.enabled', true);

        include __DIR__.'/../routes/routes.php';

        app(Kernel::class)->prependMiddleware(
            RedirectIfStageFrontIsEnabled::class
        );
    }

    /**
     * Register a test route.
     *
     * @param string $url
     * @param string $text
     */
    protected function registerRoute($url, $text)
    {
        Route::get($url, function () use ($text) {
            return $text;
        })->middleware(config('stagefront.middleware'));
    }
}
