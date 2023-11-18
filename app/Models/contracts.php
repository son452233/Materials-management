<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class contracts extends Model
{
    protected $fillable = [
        'sale_id',
        'customer_id',
        'manager_id',
        'name',
        'note',
        'status',
        'datetime_start',
        'datetime_end',
        'manager_electronic_signature',
        'customer_electronic_signature',
        'sale_eletronic_signature',
    ];

    protected $dates = [
        'datetime_start',
        'datetime_end',
        'created_at',
        'updated_at',
    ];

    public function sale()
    {
        return $this->belongsTo(User::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }


    public function deposit_amounts()
    {
        return $this->belongsToMany(deposit_amounts::class, 'contract_deposit_amount', 'contract_id', 'deposit_amount_id');
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(products::class, 'contract_details', 'contract_id', 'product_id')
            ->withPivot('amount');
    }
    public function bills()
{
    return $this->hasMany(bills::class);
}

}
