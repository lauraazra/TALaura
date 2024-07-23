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
    <div class="col-md-3">
        <form action="/dashboard/users" method="GET">
            <div class="input-group mb-3">
                <select class="form-select" name="filter">
                    <option value="active" {{ request('filter') == 'active' ? 'selected' : '' }}>Akun Aktif</option>
                    <option value="inactive" {{ request('filter') == 'inactive' ? 'selected' : '' }}>Akun Tidak Aktif</option>
                </select>
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </form>
    </div>
    <div class="col-md-9">
        <form action="/dashboard/users">
            <div class="input-group mb-3">
                <input type="text" class="form-control" placeholder="Cari Akun..." name="search" value="{{ request('search') }}">
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
                    <div class="card h-100 {{ $user->is_deleted ? 'bg-light' : '' }}">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="card-title font-weight-bold">{{ $user->name }}</h5>
                                <p class="card-text">Peran: {{ $user->role }}</p>
                                <p class="card-text">Email: {{ $user->email }}</p>
                            </div>
                            <form action="{{ $user->is_deleted ? route('users.restore', $user->id) : route('users.destroy', $user->id) }}" method="post" class="d-inline">
                                @csrf
                                @if ($user->is_deleted)
                                    @method('patch')
                                    <button class="badge bg-success border-0" onclick="return confirm('Anda yakin ingin mengaktifkan kembali akun ini?')" style="font-size: 10px;">Aktifkan</button>
                                @else
                                    @method('delete')
                                    <button class="badge bg-danger border-0" onclick="return confirm('Anda yakin ingin menghapus akun ini?')" style="font-size: 10px;">Hapus</button>
                                @endif
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
