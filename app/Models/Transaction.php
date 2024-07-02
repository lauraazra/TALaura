<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];
    protected $casts = [
        'total_item' => 'integer',
        'total_price' => 'float',
        'transaction_time' => 'datetime'
    ];
    protected $fillable = [
        'user_id', 'buyer_name', 'transaction_time', 'total_item', 'total_price', 'void'
    ];

    public function details()
    {
        return $this->hasMany(TransactionDetails::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query)
    {
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('buyer_name', 'like', '%' . $search . '%')
                    ->orWhere('transaction_time', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        if ($filter = request('filter')) {
            $now = Carbon::now();

            switch ($filter) {
                case 'last_month':
                    $startDate = $now->copy()->subMonth(1)->startOfMonth();
                    $endDate = $now->copy()->subMonth(1)->endOfMonth();
                    break;
                case 'two_months_ago':
                    $startDate = $now->copy()->subMonth(2)->startOfMonth();
                    $endDate = $now->copy()->subMonth(2)->endOfMonth();
                    break;
                case 'this_month':
                default:
                    $startDate = $now->copy()->startOfMonth();
                    $endDate = $now->copy()->endOfMonth();
                    break;
            }

            $query->whereBetween('transaction_time', [$startDate, $endDate]);
        }

        return $query;
    }
}
