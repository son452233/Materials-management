<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class request_details extends Model
{
    use HasFactory;

    protected $fillable = ['request_id', 'product_id', 'amount', 'description'];

    public function request()
    {
        return $this->belongsTo(requests::class);
    }

    public function product()
    {
        return $this->belongsTo(products::class);
    }
}
