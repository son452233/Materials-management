<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inventory_products extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_id',
        'product_id',
        'amount',
    ];
    public function inventory()
    {
        return $this->belongsTo(inventories::class);
    }

    public function product()
    {
        return $this->belongsTo(products::class);
    }
}
