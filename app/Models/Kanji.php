<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kanji extends Model
{
    use HasFactory;

    protected $table = 'kanji';

    protected $fillable = [
        'uid',
        'character',
        'meaning',
        'onyomi',
        'kunyomi',
        'level',
        'stroke_count',
        'stroke_order_gif',
        'radicals',
    ];

    protected $casts = [
        'stroke_count' => 'integer',
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
     * Get the examples for the kanji.
     */
    public function examples()
    {
        return $this->hasMany(KanjiExample::class, 'kanji_uid', 'uid');
    }

    /**
     * Get the users who favorited this kanji.
     */
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'kanji_favorites', 'kanji_uid', 'user_uid', 'uid', 'uid')
            ->withTimestamps();
    }
}
