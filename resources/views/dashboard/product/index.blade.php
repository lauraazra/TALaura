@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Kelola Produk</h1>
    <a href="{{ route('product.create') }}" class="btn btn-primary my-2">Buat Produk</a>
</div>

{{-- Search Bar --}}
<div class="row justify-content-center mb-3">
    <div class="col-md-3">
        <form action="/dashboard/product" method="GET">
            <div class="input-group mb-3">
                <select class="form-select" name="filter">
                    <option value="available" {{ request('filter') == 'available' ? 'selected' : '' }}>Produk Tersedia</option>
                    <option value="deleted" {{ request('filter') == 'deleted' ? 'selected' : '' }}>Produk Terhapus</option>
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </form>
    </div>
    <div class="col-md-9">
        <form action="/dashboard/product" method="GET">
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
<table class="table">
    <thead class="thead-dark">
        <tr>
            <th scope="col">No</th>
            <th scope="col">Produk</th>
            <th scope="col">Stok</th>
            <th scope="col">Harga</th>
            <th scope="col">Harga Grosir</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $currentPage = $products->currentPage();
            $perPage = $products->perPage();
            $startNumber = ($currentPage - 1) * $perPage + 1;
        @endphp
        @foreach ($products as $index => $product)
        <tr>
            <td>{{ $startNumber + $index }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->stok }}</td>
            <td>Rp {{ number_format($product->price, 0, ',', '.') }}</td>
            <td>
                @foreach ($product->wholesale as $price)
                    Rp {{ number_format($price->price, 0, ',', '.') }} per {{ $price->quantity }}<br>
                @endforeach
            </td>
            <td>
                <a href="{{ route('product.edit', $product->id) }}" class="badge bg-warning text-dark border-0" style="font-size: 10px;">Edit</a>
                <form action="{{ request('filter') == 'deleted' ? route('product.restore', $product->id) : route('product.destroy', $product->id) }}" method="post" class="d-inline">
                    @csrf
                    @if (request('filter') == 'deleted')
                        @method('patch')
                        <button class="badge bg-success border-0" onclick="return confirm('Anda yakin ingin mengaktifkan kembali produk ini?')" style="font-size: 10px;">Aktifkan</button>
                    @else
                        @method('delete')
                        <button class="badge bg-danger border-0" onclick="return confirm('Anda yakin ingin menghapus produk ini?')" style="font-size: 10px;">Hapus</button>
                    @endif
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
{{-- Jika produk yang dicari tidak ada --}}
<p class="text-center fs-4">Produk tidak ditemukan.</p>
@endif

{{-- Pagination Sederhana --}}
{{ $products->links() }}
@endsection
