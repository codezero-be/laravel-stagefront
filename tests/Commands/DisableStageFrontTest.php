<?php

namespace CodeZero\StageFront\Tests\Commands;

class DisableStageFrontTest extends CommandTestCase
{
    /** @test */
    public function it_sets_unencrypted_credentials()
    {
        $this->artisan('stagefront:disable')
            ->assertExitCode(0);

        $this->assertFalse($this->updater->get('STAGEFRONT_ENABLED'));
    }
}
