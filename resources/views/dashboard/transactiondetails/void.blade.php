@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Transaksi Batal</h1>
    <a href="{{ route('transaction.index') }}" class="btn btn-primary my-2">Buat Transaksi</a>
</div>

{{-- Search Bar --}}
<div class="row justify-content-center mb-3">
    <div class="col-md-6">
        <form action="{{ route('transactiondetails.void') }}" method="GET">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Cari Transaksi..." name="search" value="{{ request('search') }}">
                <button class="btn btn-success" type="submit">Cari</button>
            </div>
        </form>
    </div>
</div>

{{-- Pesan Sukses Tambah, Edit, dan Hapus Data --}}
@if (session()->has('success'))
<div class="alert alert-success alert-dismissible fade show justify-content-center mb-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Tabel Daftar Produk --}}
@if ($transactions->count())
<div class="container">
    <div class="row">
        @foreach ($transactions as $transaction)
        <div class="col-md-4 mb-3">
            <a href="{{ route('transaction.show', $transaction->id) }}" class="text-decoration-none text-dark">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title font-weight-bold">{{ \Carbon\Carbon::parse($transaction->transaction_time)->format('d-m-Y') }}</h5>
                            <p class="card-text">Pembeli: {{ $transaction->buyer_name }}</p>
                            <p class="card-text">Pembuat Nota: {{ $transaction->user->name }}</p>
                            <p class="card-text">Total Transaksi: Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@else
{{-- Jika produk yang dicari tidak ada --}}
<div id="empty-cart" class="d-flex justify-content-center align-items-center fs-2 my-5">Transaction is empty</div>
@endif

{{-- Pagination Sederhana --}}
{{ $transactions->links() }}
@endsection
