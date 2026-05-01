<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PartnershipInternship extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'name',
        'description',
        'logo_url',
        'website',
        'programs',
        'benefits',
        'contact_whatsapp',
        'contact_instagram',
        'is_verified',
        'is_active',
        'total_alumni',
        'success_rate',
        'display_order',
    ];

    protected $casts = [
        'programs' => 'array',
        'benefits' => 'array',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'total_alumni' => 'integer',
        'success_rate' => 'decimal:2',
        'display_order' => 'integer',
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
     * Get inquiries for this partner
     */
    public function inquiries()
    {
        return $this->hasMany(PartnershipInquiry::class, 'partner_uid', 'uid')
            ->where('partner_type', 'internship');
    }
}
