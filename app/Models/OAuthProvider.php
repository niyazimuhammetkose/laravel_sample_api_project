<?php

namespace App\Models;

use App\Traits\SoftDeleteAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OAuthProvider extends Model
{
    use HasFactory, SoftDeletes, SoftDeleteAttributes;

    protected $fillable = [
        'provider',
        'provider_id',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['is_deleted'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
