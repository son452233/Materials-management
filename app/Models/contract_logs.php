<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contract_logs extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'amount',
        'description',
    ];

    public function contract()
    {
        return $this->belongsTo(contracts::class, 'contract_id');
    }
}
