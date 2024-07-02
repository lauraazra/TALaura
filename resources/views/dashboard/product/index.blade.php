@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Kelola Produk</h1>
    <a href="{{ route('product.create') }}" class="btn btn-primary my-2">Buat Produk</a>
</div>

{{-- Search Bar --}}
<div class="row justify-content-center mb-3">
    <div class="col-md-6">
        <form action="/dashboard/product">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Cari Produk..." name="search" value="{{ request('search') }}">
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
@if ($products->count())
<div class="container">
    <div class="row">
        @foreach ($products as $product)
        <div class="col-md-4 mb-3">
            <a href="{{ route('product.edit', $product->id) }}" class="text-decoration-none text-dark">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title font-weight-bold">{{ $product->name }}</h5>
                            <p class="card-text">Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            <p class="card-text">Stok: {{ $product->stok }}</p>
                            @foreach ($product->wholesale as $price)
                            <p class="card-text">Harga Grosir: Rp {{ number_format($price->price, 0, ',', '.') }} per {{ $price->quantity }}</p>
                            @endforeach
                        </div>
                        <form action="{{ route('product.destroy', $product->id) }}" method="post" class="d-inline">
                            @method('delete')
                            @csrf
                            <button class="badge bg-danger border-0" onclick="return confirm('Anda yakin ingin menghapus produk ini?')" style="font-size: 10px;">Hapus</button>
                        </form>

                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@else
{{-- Jika produk yang dicari tidak ada --}}
<p class="text-center fs-4">Produk tidak ditemukan.</p>
@endif

{{-- Pagination Sederhana --}}
{{ $products->links() }}
@endsection
