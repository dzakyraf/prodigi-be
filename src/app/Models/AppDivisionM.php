<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DataStatus;

/**
 * Class AppDivisionM
 *
 * @property int $division_id
 * @property string|null $division_code
 * @property string|null $division_name
 * @property string|null $description
 * @property string|null $status
 *
 * @package App\Models
 */
class AppDivisionM extends Model
{
    protected $table = 'app_division_ms';
    protected $primaryKey = 'division_id';
    public $timestamps = false;

    protected $casts = [
        'status' => DataStatus::class,
    ];

    protected $fillable = [
        'division_code',
        'division_name',
        'description',
        'status'
    ];
}
