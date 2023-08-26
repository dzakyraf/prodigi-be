<?php

namespace App\Http\Controllers\API;

use App\Enums\DataStatus;
use App\Http\Requests\API\CreateDivisionAPIRequest;
use App\Http\Requests\API\UpdateDivisionAPIRequest;
use App\Models\Division;
use App\Repositories\DivisionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class DivisionAPIController
 */
class DivisionAPIController extends AppBaseController
{
    private DivisionRepository $divisionRepository;

    public function __construct(DivisionRepository $divisionRepo)
    {
        $this->divisionRepository = $divisionRepo;
    }

    /**
     * Display a listing of the Divisions.
     * GET|HEAD /divisions
     */
    public function index(Request $request): JsonResponse
    {
        $divisions = $this->divisionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($divisions->toArray(), 'Divisions retrieved successfully');
    }

    /**
     * Store a newly created Division in storage.
     * POST /divisions
     */
    public function store(CreateDivisionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $division = $this->divisionRepository->create($input);
        // activity()
        //     ->performedOn($anEloquentModel)
        //     ->causedBy($user)
        //     ->withProperties(['customProperty' => 'customValue'])
        //     ->log('Look mum, I logged something');


        return $this->sendResponse($division->toArray(), 'Division saved successfully');
    }

    /**
     * Display the specified Division.
     * GET|HEAD /divisions/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Division $division */
        $division = $this->divisionRepository->find($id);

        if (empty($division)) {
            return $this->sendError('Division not found');
        }

        return $this->sendResponse($division->toArray(), 'Division retrieved successfully');
    }

    /**
     * Update the specified Division in storage.
     * PUT/PATCH /divisions/{id}
     */
    public function update($id, UpdateDivisionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Division $division */
        $division = $this->divisionRepository->find($id);

        if (empty($division)) {
            return $this->sendError('Division not found');
        }

        $division = $this->divisionRepository->update($input, $id);

        return $this->sendResponse($division->toArray(), 'Division updated successfully');
    }

    /**
     * Remove the specified Division from storage.
     * DELETE /divisions/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Division $division */
        $division = $this->divisionRepository->find($id);

        if (empty($division)) {
            return $this->sendError('Division not found');
        }
        $division->update(['status' => DataStatus::Deleted]);
        $division->delete();


        return $this->sendSuccess('Division deleted successfully');
    }
}
