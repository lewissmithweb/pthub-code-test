<?php

declare(strict_types=1);

namespace App\Services;

use App\Organisation;
use App\User;
use Carbon\Carbon;

/**
 * Class OrganisationService
 * @package App\Services
 */
class OrganisationService
{
    /**
     * @param array $attributes
     *
     * @param User $user
     * @return Organisation
     */
    public function createOrganisation(array $attributes, User $user): Organisation
    {
        $organisation = new Organisation();

        $organisation->name = $attributes["name"];
        $organisation->owner_user_id = $user->id;
        $organisation->trial_end = Carbon::now()->addDays(30);

        $organisation->save();
        return $organisation;
    }

    /**
     * Filter Organisations
     *
     * @param $organisations
     * @param string|null $filter
     * @return array
     */
    public function filterOrganisations($organisations, string $filter = null): array
    {
        $orgs = [];
        foreach ($organisations as $organisation) {
            if ($filter) {
                if ($filter == 'subbed' && $organisation->subscribed) {
                    array_push($orgs, $organisation);
                }

                if ($filter == 'trial' && !$organisation->subscribed) {
                    array_push($orgs, $organisation);
                }

                continue;
            }

            array_push($orgs, $organisation);
        }

        return $orgs;
    }
}
