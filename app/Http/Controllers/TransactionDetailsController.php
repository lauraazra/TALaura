<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class TransactionDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    { 
        $query = Transaction::latest()->with('user')->where('void', 0);

        if (auth()->user()->role === 'pegawai') {
            $query->where('user_id', auth()->id());
        }

        $transactions = $query->latest()->filter()->paginate(9)->withQueryString();

        return view('dashboard.transactiondetails.index', [
            'title' => 'Riwayat Transaksi',
            'transactions' => $transactions,
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function void()
    {
        $query = Transaction::latest()->with('user')->where('void', 1);

        if (auth()->user()->role === 'pegawai') {
            $query->where('user_id', auth()->id());
        }

        $transactions = $query->latest()->filter()->paginate(9)->withQueryString();

        return view('dashboard.transactiondetails.void', [
            'title' => 'Transaksi Batal',
            'transactions' => $transactions,
        ]);
    }
}
