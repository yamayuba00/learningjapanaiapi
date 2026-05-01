<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ConversationDialog extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'conversation_id',
        'conversation_uid',
        'speaker',
        'japanese',
        'romaji',
        'indonesian',
        'dialog_order',
        'audio_url',
    ];

    protected $casts = [
        'dialog_order' => 'integer',
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

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_uid', 'uid');
    }
}
