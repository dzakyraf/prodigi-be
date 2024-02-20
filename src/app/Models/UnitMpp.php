<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
 use Illuminate\Database\Eloquent\SoftDeletes;
class UnitMpp extends Model
{
     use SoftDeletes;    public $table = 'prod_app_unit_mpp_ms';

    public $fillable = [
        'mpp_name',
        'mpp_code',
        'description',
        'is_headoffice'
    ];

    protected $casts = [
        'mpp_name' => 'string',
        'mpp_code' => 'string',
        'description' => 'string',
        'is_headoffice' => 'boolean'
    ];

    public static array $rules = [
        'mpp_name' => 'nullable|string|max:255',
        'mpp_code' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:255',
        'is_headoffice' => 'nullable|boolean'
    ];

    
}
