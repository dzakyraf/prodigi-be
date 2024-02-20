<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateUnitMppAPIRequest;
use App\Http\Requests\API\UpdateUnitMppAPIRequest;
use App\Models\UnitMpp;
use App\Repositories\UnitMppRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class UnitMppAPIController
 */
class UnitMppAPIController extends AppBaseController
{
    private UnitMppRepository $unitMppRepository;

    public function __construct(UnitMppRepository $unitMppRepo)
    {
        $this->unitMppRepository = $unitMppRepo;
    }

    /**
     * Display a listing of the UnitMpps.
     * GET|HEAD /unit-mpps
     */
    public function index(Request $request): JsonResponse
    {
        $unitMpps = $this->unitMppRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($unitMpps->toArray(), 'Unit Mpps retrieved successfully');
    }

    /**
     * Store a newly created UnitMpp in storage.
     * POST /unit-mpps
     */
    public function store(CreateUnitMppAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $unitMpp = $this->unitMppRepository->create($input);

        return $this->sendResponse($unitMpp->toArray(), 'Unit Mpp saved successfully');
    }

    /**
     * Display the specified UnitMpp.
     * GET|HEAD /unit-mpps/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var UnitMpp $unitMpp */
        $unitMpp = $this->unitMppRepository->find($id);

        if (empty($unitMpp)) {
            return $this->sendError('Unit Mpp not found');
        }

        return $this->sendResponse($unitMpp->toArray(), 'Unit Mpp retrieved successfully');
    }

    /**
     * Update the specified UnitMpp in storage.
     * PUT/PATCH /unit-mpps/{id}
     */
    public function update($id, UpdateUnitMppAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var UnitMpp $unitMpp */
        $unitMpp = $this->unitMppRepository->find($id);

        if (empty($unitMpp)) {
            return $this->sendError('Unit Mpp not found');
        }

        $unitMpp = $this->unitMppRepository->update($input, $id);

        return $this->sendResponse($unitMpp->toArray(), 'UnitMpp updated successfully');
    }

    /**
     * Remove the specified UnitMpp from storage.
     * DELETE /unit-mpps/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var UnitMpp $unitMpp */
        $unitMpp = $this->unitMppRepository->find($id);

        if (empty($unitMpp)) {
            return $this->sendError('Unit Mpp not found');
        }

        $unitMpp->delete();

        return $this->sendSuccess('Unit Mpp deleted successfully');
    }
}
