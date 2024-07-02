@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">{{ $title }}</h1>
</div>

{{-- Form Edit --}}
<div class="col-lg-5">
    <form method="post" action="{{ route('users.update', $user->id) }}" class="mb-5">
        @method('put')
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            @error('name')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required value="{{ old('name', $user->name) }}">
        </div>
        <div class="mb-3">
            <label for="position" class="form-label">Posisi</label>
            @error('position')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="text" class="form-control @error('position') is-invalid @enderror" id="position" name="position" required value="{{ old('position', $user->position) }}">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            @error('email')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" required value="{{ old('email', $user->email) }}">
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Peran</label>
            @error('role')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <select class="form-select" name="role">
                @foreach ($roles as $role)
                <option value="{{ strtolower($role) }}" {{ strtolower(old('role', $user->role)) === strtolower($role) ? 'selected' : '' }}>{{ $role }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Edit Akun</button>
    </form>

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h4">Rubah Password</h1>
    </div>
    <form method="post" action="{{ route('users.updatePassword', $user->id) }}" class="mb-5">
        @method('put')
        @csrf
        <div class="mb-3">
            <label for="current_password" class="form-label">Password Saat Ini</label>
            @error('current_password')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="password" class="form-control @error('current_password') is-invalid @enderror" id="current_password" name="current_password">
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            @error('password')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
        </div>
        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
            @error('password_confirmation')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation">
        </div>

        <button type="submit" class="btn btn-primary mb-5">Rubah Password</button>
    </form>
</div>
@endsection