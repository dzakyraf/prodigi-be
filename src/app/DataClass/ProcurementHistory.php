<?php

namespace App\DataClass;


use Spatie\LaravelData\Data;
class ProcurementHistory extends Data
{
    public function __construct(
        public int $procurement_id,
        public int $procurement_process_id,
        public string $status,
        public int $user_id,
        public string $remark,
    ) {
    }
}
