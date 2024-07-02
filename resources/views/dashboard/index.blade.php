@extends('layouts.main')

@section('container')
<style>
    .logo-dashboard img {
        width: 70px;
    }
</style>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Selamat Datang, {{ auth()->user()->name }}</h1>
</div>

{{-- Card Transaksi --}}
<div class="data-toko d-flex justify-content-center">
    <div class="card w-100 mb-3 bg-success text-white">
        <div class="card-body">
            <h5 class="card-title">Omset Transaksi Berhasil Bulan Ini</h5>
            <h1 class="card-text">
                <?php
                $startOfMonth = now()->startOfMonth()->format('Y-m-d H:i:s');
                $endOfMonth = now()->endOfMonth()->format('Y-m-d H:i:s');

                $totalTransactions = \App\Models\Transaction::where('created_at', '>=', $startOfMonth)
                    ->where('created_at', '<=', $endOfMonth)
                    ->where('void', 0)
                    ->sum('total_price');
                ?>
                {{ 'IDR ' . number_format($totalTransactions, 0, ',', '.') }}
            </h1>
        </div>
        <div class="logo-dashboard">
            <img src="/img/transaction.png" alt="Riwayat Transaksi" class="position-absolute end-0 top-50 translate-middle-y mr-5">
        </div>
        <div class="footer text-end">
            <a href="{{ route('transactiondetails.index') }}" class="text-white">
                <div class="card-footer text-body-secondary">Riwayat Transaksi >></div>
            </a>
        </div>

    </div>
</div>

{{-- Card Product --}}
<div class="data-toko d-flex justify-content-center">
    <div class="card w-100 mb-3 bg-primary text-white">
        <div class="card-body">
            <h5 class="card-title">Total Produk Terdaftar</h5>
            <h1 class="card-text">{{ \App\Models\Product::count() }}</h1>
        </div>
        <div class="logo-dashboard">
            <img src="/img/product.png" alt="Kelola Produk" class="position-absolute end-0 top-50 translate-middle-y mr-5">
        </div>
        <div class="footer text-end ">
            @if (Auth::user()->role == 'admin')
            <a href="{{ route('product.index') }}" class="text-white">
                <div class="card-footer">Kelola Produk >></div>
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Card Pengguna/User/Pegawai --}}
<div class="data-toko d-flex justify-content-center">
    <div class="card w-100 mb-3 bg-danger text-white">
        <div class="card-body">
            <h5 class="card-title">Total Akun Terdaftar</h5>
            <h1 class="card-text">{{ \App\Models\User::count() }}</h1>
        </div>
        <div class="logo-dashboard">
            <img src="/img/user.png" alt="Kelola Akun" class="position-absolute end-0 top-50 translate-middle-y mr-5">
        </div>
        <div class="footer text-end">
            @if (Auth::user()->role == 'admin')
            <a href="{{ route('users.index') }}" class="text-white">
                <div class="card-footer text-body-secondary">Kelola Akun >></div>
            </a>
            @endif
        </div>
    </div>
</div>

@endsection