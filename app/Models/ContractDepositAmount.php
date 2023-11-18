<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class ContractDepositAmount extends Model
{
    use HasFactory;
    protected $fillable = [
        'deposit_amount_id',
        'contract_id',
    ];


    public function contracts()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }

    public function deposit_amounts()
    {
        return $this->belongsTo(deposit_amounts::class, 'deposit_amount_id');
    }
}
