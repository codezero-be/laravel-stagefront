<?php

namespace CodeZero\StageFront\Tests\Commands;

class EnableStageFrontTest extends CommandTestCase
{
    /** @test */
    public function it_sets_unencrypted_credentials()
    {
        $this->artisan('stagefront:enable')
            ->assertExitCode(0);

        $this->assertTrue($this->updater->get('STAGEFRONT_ENABLED'));
    }
}
