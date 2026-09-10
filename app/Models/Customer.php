<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'birth_date',
        'blood_type',
        'nationality',
        'license_source',
        'residence_country',
        'passport_number',
        'gender',
        'license_category',
        'personal_photo',
        'local_license',
        'passport_photo',
        'license_duration',
        'status',
        'local_license_back',
        'license_number',
        'customer_code',
    ];

    /**
     * العميل يتبع لمستخدم محدد
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    protected static function booted(): void
    {
        static::creating(function ($customer) {
            // توليد كود عشوائي فريد وتشفيره بـ base64 تلقائياً قبل الحفظ
            if (empty($customer->customer_code)) {
                $rawString = Str::random(16) . time();
                $customer->customer_code = urlencode(base64_encode($rawString));
            }
        });
    }
}
