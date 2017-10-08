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
