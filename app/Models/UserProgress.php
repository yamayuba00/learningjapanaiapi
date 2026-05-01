<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserProgress extends Model
{
    use HasFactory;

    protected $table = 'user_progress';

    protected $fillable = [
        'uid',
        'user_id',
        'user_uid',
        'hiragana_score',
        'katakana_score',
        'vocabulary_score',
        'n5_progress',
        'n4_progress',
        'n3_progress',
        'n2_progress',
        'n1_progress',
        'today_lessons',
        'yesterday_lessons',
        'last_update_date',
    ];

    protected $casts = [
        'hiragana_score' => 'decimal:2',
        'katakana_score' => 'decimal:2',
        'vocabulary_score' => 'decimal:2',
        'n5_progress' => 'decimal:2',
        'n4_progress' => 'decimal:2',
        'n3_progress' => 'decimal:2',
        'n2_progress' => 'decimal:2',
        'n1_progress' => 'decimal:2',
        'today_lessons' => 'integer',
        'yesterday_lessons' => 'integer',
        'last_update_date' => 'date',
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
     * Get the user that owns the progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
