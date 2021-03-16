<?php

use App\Organisation;
use App\Services\OrganisationService;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganisationServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function createOrganisationReturnsAnOrganisation()
    {
        $user = factory(User::class)->create();

        $service = new OrganisationService();

        $this->assertInstanceOf(Organisation::class, $service->createOrganisation([
            "name" => "Test"
        ], $user));
    }

    /** @test */
    function createOrganisationSavesToDb()
    {
        $user = factory(User::class)->create();

        $service = new OrganisationService();

        $organisation = $service->createOrganisation(["name" => "Test"], $user);

        $this->assertDatabaseHas('organisations', [
            "id" => $organisation->id,
            "name" => $organisation->name
        ]);
    }

    /** @test */
    function createOrganisationHasTrialPeriodOf30Days()
    {
        Carbon::setTestNow();

        $user = factory(User::class)->create();

        $service = new OrganisationService();

        $organisation = $service->createOrganisation(["name" => "Test"], $user);

        $this->assertDatabaseHas('organisations', [
            "id" => $organisation->id,
            "trial_end" => Carbon::now()->addDays(30)->format("Y-m-d H:i:s")
        ]);
    }

    /** @test */
    function filterOrganisationsWithNullReturnsAll()
    {
        $organisation = factory(Organisation::class)->create();

        $service = new OrganisationService();

        $this->assertEquals([$organisation], $service->filterOrganisations([$organisation]));
    }

    /** @test */
    function filterOrganisationsWithSubbedReturns()
    {
        $organisation = factory(Organisation::class)->create([
            'subscribed' => true
        ]);

        $service = new OrganisationService();

        $this->assertEquals([$organisation], $service->filterOrganisations([$organisation], 'subbed'));
    }

    /** @test */
    function filterOrganisationsWithTrialReturns()
    {
        $organisation = factory(Organisation::class)->create([
            'subscribed' => false
        ]);

        $service = new OrganisationService();

        $this->assertEquals([$organisation], $service->filterOrganisations([$organisation], 'trial'));
    }
}
