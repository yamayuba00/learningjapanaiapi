<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class UserCredit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uid',
        'user_id',
        'user_uid',
        'credits',
        'total_points',
        'streak',
        'cycle_number',
        'cycle_start_date',
        'last_claim_date',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'cycle_start_date' => 'date',
        'last_claim_date' => 'date',
        'credits' => 'integer',
        'total_points' => 'integer',
        'streak' => 'integer',
        'cycle_number' => 'integer',
    ];

    /**
     * Boot function to auto-generate UUID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = (string) Str::uuid();
            }
        });
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uid';
    }

    /**
     * Get the user that owns the credit.
     * Using user_uid instead of user_id
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}

