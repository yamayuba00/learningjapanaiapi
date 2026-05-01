<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QuizHistory extends Model
{
    use HasFactory;

    protected $table = 'quiz_history';

    protected $fillable = [
        'uid',
        'user_id',
        'user_uid',
        'quiz_type',
        'category_id',
        'total_questions',
        'correct_answers',
        'wrong_answers',
        'score',
        'time_spent_seconds',
        'points_earned',
        'lives_lost',
        'taken_at',
    ];

    protected $casts = [
        'total_questions' => 'integer',
        'correct_answers' => 'integer',
        'wrong_answers' => 'integer',
        'score' => 'decimal:2',
        'time_spent_seconds' => 'integer',
        'points_earned' => 'integer',
        'lives_lost' => 'integer',
        'taken_at' => 'datetime',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
