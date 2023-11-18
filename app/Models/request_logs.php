<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class request_logs extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_id', 'note'];

    public function invoice()
    {
        return $this->belongsTo(invoice_logs::class);
    }
}
