<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'type',
        'title',
        'difficulty',
        'display_order',
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

    public function dialogs()
    {
        return $this->hasMany(ConversationDialog::class, 'conversation_uid', 'uid')
            ->orderBy('dialog_order', 'asc');
    }
}
