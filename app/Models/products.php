<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class products extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'qr_code',
        'image',
        'category_id',
    ];
    public function category()
{
    return $this->belongsTo(categories::class, 'category_id');
}

    public function contracts(): BelongsToMany
    {
        return $this->belongsToMany(contracts::class, 'contract_details', 'product_id', 'contract_id')->withPivot('amount');
    }
    public function inventories(): BelongsToMany
    {
        return $this->belongsToMany(inventories::class, 'inventory_products', 'product_id', 'inventory_id')->withPivot('amount');
    }
    public function deposit_amounts(): BelongsToMany
    {
        return $this->belongsToMany(products::class, 'contract_details', 'contract_id', 'product_id', 'deposit_amount_id')
            ->withPivot('amount');
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
    
    public function requests(): BelongsToMany
    {
        return $this->belongsToMany(requests::class, 'request_details', 'product_id', 'request_id')
            ->withPivot('amount', 'description');
    }
}
