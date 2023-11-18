<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class contract_details extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'contract_id',
        'amount',
    ];

    // Định nghĩa mối quan hệ với bảng "products"
    public function product()
    {
        return $this->belongsTo(products::class);
    }

    // Định nghĩa mối quan hệ với bảng "contracts"
    public function contract()
    {
        return $this->belongsTo(contracts::class);
    }
}
