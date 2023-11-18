<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class inventories extends Model
{
    use HasFactory;


    protected $fillable = [
        'name',
        'address',
        'phone_number',
        'manager_id'
    ];

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    // public function products()
    // {
    //     return $this->belongsToMany(products::class, 'inventory_products', 'product_id', 'inventory_id')->withPivot('amount');
    // }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(products::class, 'inventory_products', 'inventory_id', 'product_id')->withPivot('amount');
    }
}
