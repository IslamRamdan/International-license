<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'status'
    ];

    /**
     * العميل يتبع لمستخدم محدد
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
