<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Position extends Model
{
    use SoftDeletes;
    public $table = 'app_position_ms';
    protected $primaryKey = 'position_id';

    public $fillable = [
        'position_name',
        'roles_id',
        'description',
        'status'
    ];

    protected $casts = [
        'position_name' => 'string',
        'description' => 'string',
        'status' => DataStatus::class,
    ];

    public static array $rules = [
        'position_name' => 'nullable|string|max:255',
        'roles_id' => 'nullable',
        'description' => 'nullable|string|max:255',
        'status' => 'nullable|in:active,inactive,deleted',
        'created_at' => 'nullable',
        'updated_at' => 'nullable',
        'deleted_at' => 'nullable'
    ];

    public function users(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(\App\Models\User::class, 'position_id');
    }
}
