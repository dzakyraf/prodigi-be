<?php

namespace App\Repositories;

use App\Models\UnitMpp;
use App\Repositories\BaseRepository;

class UnitMppRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'mpp_name',
        'mpp_code',
        'description',
        'is_headoffice'
    ];

    public function getFieldsSearchable(): array
    {
        return $this->fieldSearchable;
    }

    public function model(): string
    {
        return UnitMpp::class;
    }
}
