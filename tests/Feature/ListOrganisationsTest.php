<?php

use App\Organisation;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListOrganisationsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function listAllOrganisationsReturnsAll()
    {
        $user = factory(User::class)->create();

        $organisationOne = factory(Organisation::class)->create([
            "owner_user_id" => $user->id,
            "subscribed" => true
        ]);
        $organisationTwo = factory(Organisation::class)->create([
            "owner_user_id" => $user->id,
            "subscribed" => true
        ]);

        $response = $this->get('/api/organisation');

        $response->assertJsonFragment([
            "name" => $organisationOne->name
        ]);
        $response->assertJsonFragment([
            "name" => $organisationTwo->name
        ]);
    }

    /** @test */
    function listAllOrganisationsReturnsSubbed()
    {
        $user = factory(User::class)->create();

        $organisationOne = factory(Organisation::class)->create([
            "owner_user_id" => $user->id,
            "subscribed" => false
        ]);
        $organisationTwo = factory(Organisation::class)->create([
            "owner_user_id" => $user->id,
            "subscribed" => true
        ]);

        $response = $this->get('/api/organisation?filter=subbed');

        $response->assertJsonMissing([
            "name" => $organisationOne->name
        ]);
        $response->assertJsonFragment([
            "name" => $organisationTwo->name
        ]);
    }

    /** @test */
    function listAllOrganisationsReturnsTrial()
    {
        $user = factory(User::class)->create();

        $organisationOne = factory(Organisation::class)->create([
            "owner_user_id" => $user->id,
            "subscribed" => true
        ]);
        $organisationTwo = factory(Organisation::class)->create([
            "owner_user_id" => $user->id,
            "subscribed" => false
        ]);

        $response = $this->get('/api/organisation?filter=trial');

        $response->assertJsonMissing([
            "name" => $organisationOne->name
        ]);
        $response->assertJsonFragment([
            "name" => $organisationTwo->name
        ]);
    }
}