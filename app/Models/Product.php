<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Hubungan tabel product dan wholesale
    public function wholesale()
    {
        return $this->hasMany(Wholesale::class);
    }

    public function transactiondetails()
    {
        return $this->hasMany(transactiondetails::class);
    }

    public function scopeFilter($query)
    {
        if (request('search')) {
            return $query->where('name', 'like', '%' . request('search') . '%');
        }
    }
}
