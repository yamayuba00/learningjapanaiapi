<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Leaderboard extends Model
{
    use HasFactory;

    protected $table = 'leaderboard';

    protected $fillable = [
        'uid',
        'user_id',
        'user_uid',
        'total_points',
        'rank',
        'month_year',
        'claims_count',
        'ads_count',
    ];

    protected $casts = [
        'total_points' => 'integer',
        'rank' => 'integer',
        'claims_count' => 'integer',
        'ads_count' => 'integer',
        'updated_at' => 'datetime',
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
     * Get the user that owns the leaderboard entry.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
