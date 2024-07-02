<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetails extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'product_id' => 'integer',
        'quantity' => 'integer',
        'price' => 'float',
        'subtotal' => 'float'
    ];
    protected $fillable = [
        'transaction_id', 'product_id', 'quantity', 'price', 'subtotal'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
