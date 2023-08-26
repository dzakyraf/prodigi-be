<?php

namespace App\Http\Controllers\API;

use App\Enums\DataStatus;
use App\Http\Requests\API\CreatePositionAPIRequest;
use App\Http\Requests\API\UpdatePositionAPIRequest;
use App\Models\Position;
use App\Repositories\PositionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;

/**
 * Class PositionAPIController
 */
class PositionAPIController extends AppBaseController
{
    private PositionRepository $positionRepository;

    public function __construct(PositionRepository $positionRepo)
    {
        $this->positionRepository = $positionRepo;
    }

    /**
     * Display a listing of the Positions.
     * GET|HEAD /positions
     */
    public function index(Request $request): JsonResponse
    {
        $positions = $this->positionRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($positions->toArray(), 'Positions retrieved successfully');
    }

    /**
     * Store a newly created Position in storage.
     * POST /positions
     */
    public function store(CreatePositionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        $position = $this->positionRepository->create($input);

        return $this->sendResponse($position->toArray(), 'Position saved successfully');
    }

    /**
     * Display the specified Position.
     * GET|HEAD /positions/{id}
     */
    public function show($id): JsonResponse
    {
        /** @var Position $position */
        $position = $this->positionRepository->find($id);

        if (empty($position)) {
            return $this->sendError('Position not found');
        }

        return $this->sendResponse($position->toArray(), 'Position retrieved successfully');
    }

    /**
     * Update the specified Position in storage.
     * PUT/PATCH /positions/{id}
     */
    public function update($id, UpdatePositionAPIRequest $request): JsonResponse
    {
        $input = $request->all();

        /** @var Position $position */
        $position = $this->positionRepository->find($id);

        if (empty($position)) {
            return $this->sendError('Position not found');
        }

        $position = $this->positionRepository->update($input, $id);

        return $this->sendResponse($position->toArray(), 'Position updated successfully');
    }

    /**
     * Remove the specified Position from storage.
     * DELETE /positions/{id}
     *
     * @throws \Exception
     */
    public function destroy($id): JsonResponse
    {
        /** @var Position $position */
        $position = $this->positionRepository->find($id);

        if (empty($position)) {
            return $this->sendError('Position not found');
        }
        $position->update(['status' => DataStatus::Deleted]);
        $position->delete();

        return $this->sendSuccess('Position deleted successfully');
    }
}
