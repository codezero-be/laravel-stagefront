<?php

namespace CodeZero\StageFront\Tests\Commands;

use CodeZero\DotEnvUpdater\DotEnvUpdater;
use CodeZero\StageFront\Tests\TestCase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class CommandTestCase extends TestCase
{
    /**
     * DotEnvUpdater instance.
     *
     * @var \CodeZero\DotEnvUpdater\DotEnvUpdater
     */
    protected $updater;

    /**
     * Path to the test .env file.
     *
     * @var string
     */
    protected $envFile;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->envFile = __DIR__ . '/../Stubs/.env';

        File::put($this->envFile, '');

        $this->updater = new DotEnvUpdater($this->envFile);

        App::instance(DotEnvUpdater::class, $this->updater);
    }

    /**
     * Clean up the testing environment before the next test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if (File::exists($this->envFile)) {
            File::delete($this->envFile);
        }

        parent::tearDown();
    }
}
