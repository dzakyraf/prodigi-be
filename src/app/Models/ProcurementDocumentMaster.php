<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementDocumentMaster extends Model
{
    protected $table = 'prod_procurement_document_list_ms';
    protected $primaryKey = 'procurement_document_list_id';
    public $timestamps = false;

    protected $fillable = [
        'process_id',
        'document_code',
        'document_title',
        'value_title',
        'filetype',
        'seq',
        'hidden',
        'data_status',
    ];

    protected $hidden = [
        'process_id',
        // 'remember_token',
    ];

    // Define relationships, if any
    // public function process()
    // {
    //     return $this->belongsTo(ProcurementProcess::class, 'process_id');
    // }
}
