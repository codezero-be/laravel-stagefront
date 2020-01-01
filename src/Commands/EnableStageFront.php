<?php

namespace CodeZero\StageFront\Commands;

use CodeZero\DotEnvUpdater\DotEnvUpdater;
use Illuminate\Console\Command;

class EnableStageFront extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stagefront:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the .env file to enable StageFront.';

    /**
     * Execute the console command.
     *
     * @param \CodeZero\DotEnvUpdater\DotEnvUpdater $updater
     *
     * @return void
     */
    public function handle(DotEnvUpdater $updater)
    {
        $updater->set('STAGEFRONT_ENABLED', true);

        $this->info("StageFront has been enabled.");
    }
}
