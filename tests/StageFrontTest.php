<?php

namespace CodeZero\StageFront\Tests;

use CodeZero\StageFront\Middleware\RedirectIfStageFrontIsEnabled;
use CodeZero\StageFront\Tests\Stubs\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class StageFrontTest extends TestCase
{
    /**
     * StageFront URL
     *
     * @var string
     */
    protected $url;

    /** @test */
    public function it_redirects_to_a_login_screen_when_stagefront_is_enabled()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->enableStageFront();

        $this->get('/page')->assertRedirect($this->url);
    }

    /** @test */
    public function it_does_not_redirect_to_a_login_screen_when_stagefront_is_disabled()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->get('/page')->assertStatus(200)->assertSee('Some Page');
    }

    /** @test */
    public function the_login_route_does_not_exist_when_stagefront_is_disabled()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->get($this->url)->assertStatus(404);
    }

    /** @test */
    public function it_redirects_to_the_intended_url_when_you_provide_valid_credentials()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        Config::set('stagefront.login', 'tester');
        Config::set('stagefront.password', 'p4ssw0rd');

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
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        Config::set('stagefront.login', 'tester');
        Config::set('stagefront.password', 'p4ssw0rd');

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
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->enableStageFront();

        Session::put('stagefront.unlocked', true);

        $this->get($this->url)->assertRedirect('/');
    }

    /** @test */
    public function the_password_may_be_stored_encrypted()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        Config::set('stagefront.login', 'tester');
        Config::set('stagefront.password', Hash::make('p4ssw0rd'));
        Config::set('stagefront.encrypted', true);

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
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->loadLaravelMigrations(['--database' => 'testing']);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);

        Config::set('stagefront.database', true);
        Config::set('stagefront.database_table', 'users');
        Config::set('stagefront.database_login_field', 'email');
        Config::set('stagefront.database_password_field', 'password');

        $this->enableStageFront();
        $this->setIntendedUrl('/page');

        $response = $this->submitForm([
            'login' => 'john@doe.io',
            'password' => 'str0ng p4ssw0rd',
        ]);

        $response->assertRedirect('/page');
    }

    /** @test */
    public function you_can_limit_which_database_users_have_access_using_a_comma_separated_string()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->loadLaravelMigrations(['--database' => 'testing']);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@doe.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);
        User::create([
            'name' => 'Mr. Smith',
            'email' => 'mr@smith.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);

        Config::set('stagefront.database', true);
        Config::set('stagefront.database_whitelist', 'john@doe.io , jane@doe.io');
        Config::set('stagefront.database_table', 'users');
        Config::set('stagefront.database_login_field', 'email');
        Config::set('stagefront.database_password_field', 'password');

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
    public function you_can_limit_which_database_users_have_access_using_an_array()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->loadLaravelMigrations(['--database' => 'testing']);

        User::create([
            'name' => 'John Doe',
            'email' => 'john@doe.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);
        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@doe.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);
        User::create([
            'name' => 'Mr. Smith',
            'email' => 'mr@smith.io',
            'password' => Hash::make('str0ng p4ssw0rd'),
        ]);

        Config::set('stagefront.database', true);
        Config::set('stagefront.database_whitelist', ['john@doe.io', ' jane@doe.io ']);
        Config::set('stagefront.database_table', 'users');
        Config::set('stagefront.database_login_field', 'email');
        Config::set('stagefront.database_password_field', 'password');

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
    public function it_allows_access_to_whitelisted_ips_only()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->enableStageFront();
        $this->setIntendedUrl('/page');

        Config::set('stagefront.ip_whitelist', ' 0.0.0.0 , 1.1.1.1 ');
        Config::set('stagefront.ip_whitelist_only', true);
        Config::set('stagefront.ip_whitelist_require_login', false);

        $this->get('/page', ['REMOTE_ADDR' => '1.2.3.4'])
            ->assertStatus(403);

        $this->get('/page', ['REMOTE_ADDR' => '1.1.1.1'])
            ->assertOk();
    }

    /** @test */
    public function you_can_add_a_whitelist_as_an_array()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->enableStageFront();
        $this->setIntendedUrl('/page');

        Config::set('stagefront.ip_whitelist', ['0.0.0.0', '1.1.1.1']);
        Config::set('stagefront.ip_whitelist_only', true);
        Config::set('stagefront.ip_whitelist_require_login', false);

        $this->get('/page', ['REMOTE_ADDR' => '1.2.3.4'])
            ->assertStatus(403);

        $this->get('/page', ['REMOTE_ADDR' => '1.1.1.1'])
            ->assertOk();
    }

    /** @test */
    public function it_allows_access_to_whitelisted_ips_only_with_required_login()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->enableStageFront();
        $this->setIntendedUrl('/page');

        Config::set('stagefront.login', 'tester');
        Config::set('stagefront.password', 'p4ssw0rd');
        Config::set('stagefront.ip_whitelist', ' 0.0.0.0 , 1.1.1.1 ');
        Config::set('stagefront.ip_whitelist_only', true);
        Config::set('stagefront.ip_whitelist_require_login', true);

        $this->get('/page', ['REMOTE_ADDR' => '1.2.3.4'])
            ->assertStatus(403);

        $this->get('/page', ['REMOTE_ADDR' => '1.1.1.1'])
            ->assertRedirect($this->url);

        $response = $this->submitForm([
            'login' => 'tester',
            'password' => 'p4ssw0rd',
        ], ['REMOTE_ADDR' => '1.1.1.1']);

        $response->assertRedirect('/page');
    }

    /** @test */
    public function it_allows_instant_access_to_whitelisted_ips_and_password_access_to_other_ips()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->enableStageFront();
        $this->setIntendedUrl('/page');

        Config::set('stagefront.login', 'tester');
        Config::set('stagefront.password', 'p4ssw0rd');
        Config::set('stagefront.ip_whitelist', ' 0.0.0.0 , 1.1.1.1 ');
        Config::set('stagefront.ip_whitelist_only', false);
        Config::set('stagefront.ip_whitelist_require_login', false);

        $this->get('/page', ['REMOTE_ADDR' => '1.2.3.4'])
            ->assertRedirect($this->url);

        $this->get('/page', ['REMOTE_ADDR' => '1.1.1.1'])
            ->assertOk();
    }

    /** @test */
    public function urls_can_be_ignored_so_access_is_not_denied_by_stagefront()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->registerRoute('/public', 'Public');
        $this->registerRoute('/public/route', 'Route');

        Config::set('stagefront.ignore_urls', ['/public/*']);

        $this->enableStageFront();

        $this->get('/public')->assertRedirect($this->url);
        $this->get('/public/route')->assertStatus(200)->assertSee('Route');
    }

    /** @test */
    public function domains_can_be_ignored_so_access_is_not_denied_by_stagefront()
    {
        $this->url = Config::get('stagefront.url');

        $this->registerRouteWithDomain('/admin', 'Admin');

        Config::set('stagefront.ignore_domains', ['domain.example.com']);

        $this->enableStageFront();

        $this->call('GET', 'http://domain.example.com/admin')->assertStatus(200)->assertSee('Admin');
    }

    /** @test */
    public function ignored_urls_can_be_accessed_by_non_whitelisted_ips()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $this->registerRoute('/public', 'Public');
        $this->registerRoute('/public/route', 'Route');

        Config::set('stagefront.ignore_urls', ['/public/*']);
        Config::set('stagefront.ip_whitelist', '0.0.0.0');
        Config::set('stagefront.ip_whitelist_only', true);
        Config::set('stagefront.ip_whitelist_require_login', false);

        $this->enableStageFront();

        $this->get('/public', ['REMOTE_ADDR' => '1.2.3.4'])->assertStatus(403);
        $this->get('/public/route', ['REMOTE_ADDR' => '1.2.3.4'])->assertStatus(200)->assertSee('Route');
    }

    /** @test */
    public function it_throttles_login_attempts()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $faultyCredentials = [
            'login' => 'tester',
            'password' => 'invalid',
        ];

        Config::set('stagefront.throttle', true);
        Config::set('stagefront.throttle_tries', 2);
        Config::set('stagefront.throttle_delay', 2);

        $this->enableStageFront();

        $this->submitForm($faultyCredentials)->assertRedirect($this->url);
        $this->submitForm($faultyCredentials)->assertRedirect($this->url);
        $this->submitForm($faultyCredentials)->assertStatus(429);
    }

    /** @test */
    public function throttling_login_attempts_can_be_disabled()
    {
        $this->url = Config::get('stagefront.url');
        $this->registerRoute('/page', 'Some Page');

        $faultyCredentials = [
            'login' => 'tester',
            'password' => 'invalid',
        ];

        Config::set('stagefront.throttle', false);
        Config::set('stagefront.throttle_tries', 2);
        Config::set('stagefront.throttle_delay', 2);

        $this->enableStageFront();

        $this->submitForm($faultyCredentials)->assertRedirect($this->url);
        $this->submitForm($faultyCredentials)->assertRedirect($this->url);
        $this->submitForm($faultyCredentials)->assertRedirect($this->url);
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
        Session::put('url.intended', $url);

        return $this;
    }

    /**
     * Send a post request.
     *
     * @param array $credentials
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function submitForm(array $credentials, $headers = [])
    {
        $headers += [
            // Since we're calling routes directly,
            // we need to fake the referring page
            // so that redirect()->back() will work.
            'HTTP_REFERER' => $this->url,
        ];

        return $this->post($this->url, $credentials, $headers);
    }

    /**
     * Enable StageFront.
     * Routes and middleware haven't been loaded, so we should do this now.
     *
     * @return void
     */
    protected function enableStageFront()
    {
        Config::set('stagefront.enabled', true);

        include __DIR__.'/../routes/routes.php';

        App::make(Kernel::class)->prependMiddleware(
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
        })->middleware(Config::get('stagefront.middleware'));
    }

    /**
     * Register a test route.
     *
     * @param string $url
     * @param string $text
     */
    protected function registerRouteWithDomain($url, $text)
    {
        Route::domain('domain.example.com')->group(function () use ($url, $text) {
            Route::get($url, function () use ($text) {
                return $text;
            })->middleware(Config::get('stagefront.middleware'));
        });
    }
}
