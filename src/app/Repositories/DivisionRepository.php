<?php

namespace App\Repositories;

use App\Models\Division;
use App\Repositories\BaseRepository;

class DivisionRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'division_code',
        'division_name',
        'description',
        'status',
        'id'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return Division::class;
    }
}
