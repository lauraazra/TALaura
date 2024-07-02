@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Kelola Akun</h1>
    {{-- Tombol Buat Akun Baru --}}
    <a href="{{ route('users.create') }}" class="btn btn-primary my-2">Buat Akun</a>
</div>

{{-- Search Bar --}}
<div class="row justify-content-center mb-3">
    <div class="col-md-6">
        <form action="/dashboard/users">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Cari Akun..." name="search" value="{{request('search')}}">
                <button class="btn btn-success" type="submit">Cari</button>
            </div>
        </form>
    </div>
</div>

{{-- Pesan Sukses CRUD Akun --}}
@if (session()->has('success'))
<div class="alert alert-success alert-dismissible fade show justify-content-center mb-3" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

{{-- Tabel Daftar Produk --}}
@if ($users->count())
<div class="container">
    <div class="row">
        @foreach ($users as $user)
        <div class="col-md-4 mb-3">
            <a href="{{ route('users.edit', $user->id) }}" class="text-decoration-none text-dark">
                <div class="card h-100">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title font-weight-bold">{{ $user->name }}</h5>
                            <p class="card-text">Peran: {{ $user->role }}</p>
                            <p class="card-text">Email: {{ $user->email }}</p>
                        </div>
                        <form action="{{ route('users.destroy', $user->id) }}" method="post" class="d-inline">
                            @method('delete')
                            @csrf
                            {{-- Tombol Hapus Akun --}}
                            <button class="badge bg-danger border-0" onclick="return confirm('Apakah Anda Yakin?')" style="font-size: 10px" ;>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>
@else
{{-- Jika akun yang dicari tidak ada --}}
<p class="text-center fs-4">Akun tidak ditemukan.</p>
@endif

{{-- Pagination Halaman --}}
{{ $users->links() }}
@endsection