<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\SoftDeleteAttributes;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'User',
    properties: [
        'name' => new OA\Property(property: 'name', type: 'string'),
        'email' => new OA\Property(property: 'email', type: 'string'),
        'password' => new OA\Property(property: 'password', type: 'string'),
    ]
)]
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, SoftDeletes, SoftDeleteAttributes, HasApiTokens, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_email_verified', 'is_deleted', 'is_two_factor_enabled'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determine if the user is an administrator.
     */
    protected function isEmailVerified(): Attribute
    {
        return new Attribute(
            get: fn() => !is_null($this->email_verified_at),
        );
    }

    /**
     * Determine if the user is an administrator.
     */
    protected function isTwoFactorEnabled(): Attribute
    {
        return new Attribute(
            get: fn() => !is_null($this->two_factor_confirmed_at),
//            get: fn() => !is_null($this->two_factor_secret),
        );
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('d/m/Y H:i:s');
    }

    public function OAuthProviders(): HasMany
    {
        return $this->hasMany(OAuthProvider::class);
    }

}
