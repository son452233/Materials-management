<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class invoices extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'invoice_number', 'invoice_date'];

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }
}
