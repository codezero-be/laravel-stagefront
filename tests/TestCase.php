<?php

namespace CodeZero\StageFront\Tests;

use CodeZero\StageFront\Middleware\RedirectIfStageFrontIsEnabled;
use CodeZero\StageFront\StageFrontServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        parent::setUp();

        config()->set('app.key', str_random(32));

        app(Kernel::class)->prependMiddleware(
            RedirectIfStageFrontIsEnabled::class
        );
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            StageFrontServiceProvider::class,
        ];
    }
}
