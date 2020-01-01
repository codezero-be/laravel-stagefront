<?php

namespace CodeZero\StageFront\Tests;

use CodeZero\DotEnvUpdater\Laravel\DotEnvUpdaterServiceProvider;
use CodeZero\StageFront\StageFrontServiceProvider;
use Illuminate\Support\Str;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', Str::random(32));
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
            DotEnvUpdaterServiceProvider::class,
            StageFrontServiceProvider::class,
        ];
    }
}
