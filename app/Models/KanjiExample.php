<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KanjiExample extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'kanji_id',
        'kanji_uid',
        'word',
        'reading',
        'meaning',
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
     * Get the kanji that owns the example.
     */
    public function kanji()
    {
        return $this->belongsTo(Kanji::class, 'kanji_uid', 'uid');
    }
}
