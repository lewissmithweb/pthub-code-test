<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Notifications\OrganisationCreated;
use App\Organisation;
use App\Services\OrganisationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

/**
 * Class OrganisationController
 * @package App\Http\Controllers
 */
class OrganisationController extends ApiController
{
    /**
     * @var Organisation $organisation
     */
    protected $organisation;

    public function __construct(Request $request, Organisation $organisation)
    {
        parent::__construct($request);
        $this->organisation = $organisation;
    }

    /**
     * Store organisation
     *
     * @param OrganisationService $service
     *
     * @return JsonResponse
     */
    public function store(OrganisationService $service): JsonResponse
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), Response::HTTP_BAD_REQUEST);
        }

        /** @var Organisation $organisation */
        $organisation = $service->createOrganisation($this->request->all(), $this->request->user());

        $this->request->user()->notify(new OrganisationCreated($organisation));

        return $this
            ->transformItem('organisation', $organisation, $this->request->user())
            ->respond();
    }

    /**
     * List all organisations
     *
     * @param OrganisationService $service
     * @return JsonResponse
     */
    public function listAll(OrganisationService $service): JsonResponse
    {
        $filter = $this->request->input('filter');

        $organisations = $this->organisation->all();

        $orgs = $service->filterOrganisations($organisations, $filter);

        return $this
            ->transformCollection('organisations', $orgs)
            ->respond();
    }
}
