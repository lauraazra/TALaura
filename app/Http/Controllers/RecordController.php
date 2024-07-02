<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecordController extends Controller
{
    public function index(Request $request)
    {
        // Query untuk data transaksi
        $query = Transaction::query();
    
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('buyer_name', 'like', '%' . $search . '%')
                  ->orWhere('transaction_time', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
    
        // Filter berdasarkan status transaksi (gagal/berhasil)
        if ($request->has('status')) {
            if ($request->status == 'gagal') {
                $query->where('void', 1);
            } elseif ($request->status == 'berhasil') {
                $query->where('void', 0);
            }
        }
    
        // Filter berdasarkan periode waktu
        if ($request->has('filter')) {
            if ($request->filter == 'this_month') {
                $query->whereMonth('transaction_time', now()->month);
            } elseif ($request->filter == 'last_month') {
                $query->whereMonth('transaction_time', now()->subMonth()->month);
            } elseif ($request->filter == 'two_months_ago') {
                $query->whereMonth('transaction_time', now()->subMonths(2)->month);
            }
        }
    
        // Query untuk omset perbulan
        $monthlySalesQuery = Transaction::selectRaw('YEAR(transaction_time) as year, MONTH(transaction_time) as month, SUM(total_price) as total_sales')
            ->where('void', 0) // Hanya transaksi dengan void=0
            ->groupByRaw('YEAR(transaction_time), MONTH(transaction_time)');
    
        // Filter berdasarkan bulan dan tahun
        if ($request->has('filter_month') && $request->has('filter_year')) {
            $monthlySalesQuery->whereMonth('transaction_time', $request->filter_month)
                              ->whereYear('transaction_time', $request->filter_year);
        }
    
        // Mengambil daftar bulan dan tahun yang ada di tabel transactions
        $monthlySalesMonths = Transaction::selectRaw('MONTH(transaction_time) as month')
            ->distinct()
            ->orderBy('month')
            ->pluck('month');
    
        $monthlySalesYears = Transaction::selectRaw('YEAR(transaction_time) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        $monthlySales = $monthlySalesQuery->orderByRaw('YEAR(transaction_time) DESC, MONTH(transaction_time) DESC')
            ->paginate(5, ['*'], 'monthlySalesPage')->withQueryString(); // Ubah ini untuk mengatur jumlah item per halaman
    
        // Query untuk omset per kasir atau user
        $monthlySalesByUserQuery = Transaction::selectRaw('users.name as kasir, YEAR(transaction_time) as year, MONTH(transaction_time) as month, SUM(total_price) as total_sales')
            ->join('users', 'transactions.user_id', '=', 'users.id')
            ->where('void', 0) // Hanya transaksi dengan void=0
            ->groupByRaw('users.name, YEAR(transaction_time), MONTH(transaction_time)');
    
        // Filter berdasarkan kasir/user, bulan, dan tahun
        if ($request->has('filter_user') && $request->has('filter_month_user') && $request->has('filter_year_user')) {
            $monthlySalesByUserQuery->where('transactions.user_id', $request->filter_user)
                                    ->whereMonth('transaction_time', $request->filter_month_user)
                                    ->whereYear('transaction_time', $request->filter_year_user);
        }
    
        // Mengambil daftar bulan dan tahun untuk omset per kasir atau user
        $monthlySalesByUserMonths = Transaction::selectRaw('MONTH(transaction_time) as month')
            ->distinct()
            ->orderBy('month')
            ->pluck('month');
    
        $monthlySalesByUserYears = Transaction::selectRaw('YEAR(transaction_time) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        $monthlySalesByUser = $monthlySalesByUserQuery->orderByRaw('YEAR(transaction_time) DESC, MONTH(transaction_time) DESC')
            ->paginate(5, ['*'], 'monthlySalesByUserPage')->withQueryString(); // Ubah ini untuk mengatur jumlah item per halaman
    
        // Data untuk dropdown filter bulan dan kasir/user
        $availableMonths = [
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
    
        // Mengambil daftar tahun dari transactions untuk omset perbulan
        $currentYear = date('Y');
        $availableYears = Transaction::selectRaw('YEAR(transaction_time) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');
    
        // Data untuk dropdown filter kasir/user
        $availableUsers = User::orderBy('name')->get();
    
        // Pagination dan data transaksi
        $transactions = $query->latest()->paginate(7)->withQueryString();
    
        return view('dashboard.record', [
            'title' => 'Record',
            'transactions' => $transactions,
            'monthlySales' => $monthlySales,
            'monthlySalesByUser' => $monthlySalesByUser,
            'availableMonths' => $availableMonths,
            'availableYears' => $availableYears,
            'availableUsers' => $availableUsers,
            'monthlySalesMonths' => $monthlySalesMonths, // Untuk opsi bulan omset perbulan
            'monthlySalesYears' => $monthlySalesYears,   // Untuk opsi tahun omset perbulan
            'monthlySalesByUserMonths' => $monthlySalesByUserMonths, // Untuk opsi bulan omset per kasir/user
            'monthlySalesByUserYears' => $monthlySalesByUserYears,   // Untuk opsi tahun omset per kasir/user
        ]);
    }
}