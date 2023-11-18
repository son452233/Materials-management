<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class deposit_amounts extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'price',
        'total_price',
        'start_date',
        'end_date',
        'percent',
        'number_of_payments',
        'percent_amount',
        'remaining_amount',
        'product_id',
        'payment_details',
        'status',
    ];
    public function product()
    {
        return $this->belongsTo(products::class);
    }
    public function contracts()
    {
        return $this->belongsToMany(contracts::class, 'contract_deposit_amount', 'deposit_amount_id', 'contract_id');
    }
    public function paymentDetails()
{
    return $this->hasMany(PaymentDetail::class, 'deposit_amount_id');
}

    public function depositAmountLogs()
    {
        return $this->hasMany(deposit_amount_logs::class);
    }
    
}
