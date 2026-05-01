<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class VocabularyCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'icon',
        'display_order',
    ];

    protected $casts = [
        'display_order' => 'integer',
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
     * Get the words for the category.
     */
    public function words()
    {
        return $this->hasMany(VocabularyWord::class, 'category_uid', 'uid');
    }
}
