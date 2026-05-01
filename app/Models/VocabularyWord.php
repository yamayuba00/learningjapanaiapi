<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VocabularyWord extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'category_id',
        'category_uid',
        'japanese',
        'romaji',
        'indonesian',
        'level',
        'example_sentence_jp',
        'example_sentence_romaji',
        'example_sentence_id',
        'audio_url',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

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
     * Get the category that owns the word.
     */
    public function category()
    {
        return $this->belongsTo(VocabularyCategory::class, 'category_uid', 'uid');
    }

    /**
     * Get the users who favorited this word.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'vocabulary_favorites', 'word_uid', 'user_uid', 'uid', 'uid')
            ->withTimestamps();
    }
}
