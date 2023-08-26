<?php

namespace App\Models;

use App\Enums\DataStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Division extends Model
{
    use LogsActivity;
    use SoftDeletes;

    /**
     * Summary of table
     * @var string
     */
    public $table = 'app_division_ms';
    /**
     * Summary of primaryKey
     * @var string
     */
    protected $primaryKey = 'division_id';

    /**
     * Summary of fillable
     * @var array
     */
    public $fillable = [
        'division_code',
        'division_name',
        'description',
        'status',
        'id'
    ];

    /**
     * Summary of casts
     * @var array
     */
    protected $casts = [
        'division_code' => 'string',
        'division_name' => 'string',
        'description' => 'string',
        'status' => DataStatus::class,
    ];

    /**
     * Summary of rules
     * @var array
     */
    public static array $rules = [
        'division_code' => 'nullable|string|max:255',
        'division_name' => 'nullable|string|max:255',
        'description' => 'nullable|string|max:255',
        'status' => 'nullable|in:active,inactive,deleted',
        'updated_at' => 'nullable',
        'created_at' => 'nullable'
    ];
    /**
     * @return LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'division_code',
                'division_name',
                'description',
                'status',
                'id'
            ]);
        // Chain fluent methods for configuration options
    }
}
