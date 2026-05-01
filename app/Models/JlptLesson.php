<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JlptLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'user_id',
        'user_uid',
        'level',
        'lesson_index',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'lesson_index' => 'integer',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
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
     * Get the user that owns the lesson.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }
}
