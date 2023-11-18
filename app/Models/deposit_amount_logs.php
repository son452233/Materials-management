<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class deposit_amount_logs extends Model
{
    use HasFactory;
    protected $fillable = [
        'deposit_amount_id',
        'amount',
        'status',
        'note',
    ];

    public function depositAmount()
    {
        return $this->belongsTo(deposit_amounts::class);
    }
    
}
