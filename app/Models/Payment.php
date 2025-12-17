<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_SUCCESS = 'success';
    public const STATUS_FAILED = 'failed';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'transaction_id',
        'user_id',
        'payment_method',
        'payment_gateway',
        'gateway_reference',
        'snap_token',
        'amount',
        'status',
        'response_payload',
        'failure_reason',
        'paid_at',
    ];

    protected $casts = [
        'response_payload' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
