<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Casts\PgIntArray;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public $table = 'prod_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'unit_mpp_id',
        'position_id',
        'roles_id',
        'division_id',
    ];



    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'unit_mpp_id',
        'position_id',
        'division_id',
        'roles_id'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'position_id' => 'integer',
        'unit_mpp_id' => PgIntArray::class,
        // 'roles_id' => PgIntArray::class,
        'division_id' => 'integer'
    ];

    protected function roles(): Attribute
    {
        $ids = $this->attributes['roles_id'];
        $data =  DB::table('prod_user_roles as pur')
        ->selectRaw("roles_code")
        ->whereIn('user_roles_id',  explode(',',str_replace(array('{', '}'), '', $ids)))
        ->get()
        ->pluck("roles_code")->toArray();

        return Attribute::make(
            get: fn () => $data,
        );
    }

    protected function unitMpp(): Attribute
    {
        $ids = $this->attributes['unit_mpp_id'];

        $data =  DB::table('prod_app_unit_mpp_ms')
        ->selectRaw("mpp_name")
        ->whereIn('unit_mpp_id',  explode(',',str_replace(array('{', '}'), '', $ids)))
        ->get()
        ->pluck("mpp_name")->toArray();

        return Attribute::make(
            get: fn () => $data,
        );
    }



    protected $appends = ['roles,unit_mpp,position'];


}
