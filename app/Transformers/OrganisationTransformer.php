<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Organisation;
use Carbon\Carbon;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

/**
 * Class OrganisationTransformer
 * @package App\Transformers
 */
class OrganisationTransformer extends TransformerAbstract
{
    /**
     * Available resources to include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'user'
    ];

    /**
     * @param Organisation $organisation
     *
     * @return array
     */
    public function transform(Organisation $organisation): array
    {
        $trailEnd = "";
        if (!$organisation->subscribed) {
            $trailEnd = Carbon::parse($organisation->trial_end);
            $trailEnd = (string)$trailEnd->unix();
        }

        return [
            "name" => $organisation->name,
            "trial_end" => $trailEnd
        ];
    }

    /**
     * @param Organisation $organisation
     *
     * @return Item
     */
    public function includeUser(Organisation $organisation)
    {
        return $this->item($organisation->user, new UserTransformer());
    }
}
