<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'description',
        'product_id',
    ];
    public function product()
    {
        return $this->belongsTo(products::class);
    }
}
