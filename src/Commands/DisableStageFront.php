<?php

namespace CodeZero\StageFront\Commands;

use CodeZero\DotEnvUpdater\DotEnvUpdater;
use Illuminate\Console\Command;

class DisableStageFront extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stagefront:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the .env file to disable StageFront.';

    /**
     * Execute the console command.
     *
     * @param \CodeZero\DotEnvUpdater\DotEnvUpdater $updater
     *
     * @return void
     */
    public function handle(DotEnvUpdater $updater)
    {
        $updater->set('STAGEFRONT_ENABLED', false);

        $this->info("StageFront has been disabled.");
    }
}
