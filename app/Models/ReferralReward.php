<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReferralReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'referrer_user_id',
        'referrer_user_uid',
        'referred_user_id',
        'referred_user_uid',
        'referrer_credits_earned',
        'referred_credits_earned',
    ];

    protected $casts = [
        'referrer_credits_earned' => 'integer',
        'referred_credits_earned' => 'integer',
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

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referrer_user_uid', 'uid');
    }

    public function referred()
    {
        return $this->belongsTo(User::class, 'referred_user_uid', 'uid');
    }
}
