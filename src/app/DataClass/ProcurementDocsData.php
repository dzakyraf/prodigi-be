<?php

namespace App\DataClass;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class ProcurementDocsData extends Data
{
    public function __construct(
        public int $procurement_process_id,
        public int $procurement_id,
        public string|Optional $document_no = '',
        public string|Optional $document_date = '',
        public string $value,
        public string $document_type,
        public int $document_ms_id,
    ) {
    }
}
