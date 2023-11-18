<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class requests extends Model
{
    use HasFactory;

    protected $fillable = [
        "sale_id",
        "customer_id",
        "name",
        "note",
        "datetime",
    ];

    public function sale()
    {
        return $this->belongsTo(User::class, 'sale_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(requests::class, 'request_details', 'request_id', 'product_id')
            ->withPivot('amount', 'description');
    }
}
