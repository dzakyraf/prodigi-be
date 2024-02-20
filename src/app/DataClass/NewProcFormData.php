<?php

namespace App\DataClass;


use App\Cast\PgAarray;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithTransformer;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class NewProcFormData extends Data
{
    public function __construct(
        public string $name,
        public string $nodin,
        public string $division_id,
        #[WithTransformer(PgAarray::class)]
        public string $unit_mpp_id,
        public string $nodin_date,
        // public int $procurement_type_id,
        public int $rab_amount,
        public int $last_process_id,
        public string $last_status,
        public int|Optional $po_progress = 0,
        public bool|Optional $is_closed = false,
        public int $creator_id,
        // #[DataCollectionOf(ProcDocForm::class)]
        // public ProcurementDocsData $documents,

    ) {
    }
}


