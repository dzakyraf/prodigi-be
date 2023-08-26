<?php

namespace App\Repositories;

use App\Models\Position;
use App\Repositories\BaseRepository;

class PositionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'position_name',
        'roles_id',
        'description'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Position::class;
    }
}
