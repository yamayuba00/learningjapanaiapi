<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DailyLoginClaim extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'user_id',
        'user_uid',
        'cycle_number',
        'day_index',
        'points_earned',
        'credits_earned',
        'claimed_at',
    ];

    protected $casts = [
        'cycle_number' => 'integer',
        'day_index' => 'integer',
        'points_earned' => 'integer',
        'credits_earned' => 'integer',
        'claimed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = (string) Str::uuid();
            }
            
            // Auto-fill user_id if not provided but user_uid is available
            if (empty($model->user_id) && !empty($model->user_uid)) {
                $user = \App\Models\User::where('uid', $model->user_uid)->first();
                if ($user) {
                    $model->user_id = $user->id;
                }
            }
        });
    }

    /**
     * Get the user that owns the daily login claim.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
