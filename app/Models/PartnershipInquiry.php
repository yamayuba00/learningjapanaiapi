<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PartnershipInquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'inquiry_code',
        'user_uid',
        'partner_uid',
        'partner_type',
        'message',
        'preferred_contact',
        'status',
    ];

    protected $casts = [
        'partner_type' => 'string',
        'preferred_contact' => 'string',
        'status' => 'string',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uid)) {
                $model->uid = (string) Str::uuid();
            }
            if (empty($model->inquiry_code)) {
                $model->inquiry_code = 'INQ' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Get the user who made the inquiry
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uid', 'uid');
    }

    /**
     * Get the partner (polymorphic-like)
     */
    public function partner()
    {
        if ($this->partner_type === 'jlpt_class') {
            return PartnershipJlptClass::where('uid', $this->partner_uid)->first();
        } else {
            return PartnershipInternship::where('uid', $this->partner_uid)->first();
        }
    }
}
