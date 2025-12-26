<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'label',
    'recipient_name',
    'phone',
    'address_text',
    'province',
    'city',
    'district_id',
    'district',
    'subdistrict',
    'postal_code',
    'is_primary',
];


    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
