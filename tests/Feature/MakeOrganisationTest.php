<?php

use App\Notifications\OrganisationCreated;
use App\Organisation;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MakeOrganisationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function storeWithoutNameErrors()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->post('/api/organisation', []);

        $response->assertJsonFragment(["The name field is required."]);
    }

    /** @test */
    function storeWithoutAuthErrors()
    {
        $response = $this->post('/api/organisation', []);

        $response->assertStatus(302);
    }

    /** @test */
    function storeSendsNotification()
    {
        Notification::fake();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $this->post('/api/organisation', [
            "name" => "Test Organisation"
        ]);

        Notification::assertSentTo($user, OrganisationCreated::class);
    }

    /** @test */
    function storeReturnsCorrectData()
    {
        Carbon::setTestNow();
        Notification::fake();

        $user = factory(User::class)->create();
        Passport::actingAs($user);

        $response = $this->post('/api/organisation', [
            "name" => "Test Organisation"
        ]);

        $response->assertJsonFragment([
            "name" => "Test Organisation",
            "trial_end" => (string)Carbon::now()->addDays(30)->unix(),
            "name" => $user->name
        ]);
    }
}
