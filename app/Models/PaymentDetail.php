<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'deposit_amount_id',
        'payment_number',
        'payment_amount',
        'remaining_amount',
        'status',
    ];

    // Define relationship to DepositAmount model
    public function deposit_amounts()
    {
        return $this->belongsTo(deposit_amounts::class, 'deposit_amount_id');
    }
}
