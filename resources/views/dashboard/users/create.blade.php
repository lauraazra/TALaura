@extends('layouts.main')

@section('container')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Buat Akun</h1>
</div>

<div class="col-lg-5">
    <form method="post" action="{{ route('users.store') }}" class="mb-5">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            @error('name')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="position" class="form-label">Posisi</label> <!-- Corrected from "positition" to "position" -->
            @error('position')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="text" class="form-control" id="position" name="position" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            @error('email')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">Peran</label>
            @error('role')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <select class="form-select" aria-label="Default select example" name="role" id="role">
                <option selected>Pilih Role Akun</option>
                <option value="admin" {{ strtolower(old('role'))=="admin" ? 'selected':'' }}>Admin</option>
                <option value="pegawai" {{ strtolower(old('role'))=="pegawai" ? 'selected':'' }}>Pegawai</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            @error('password')
            <div class="text-danger fst-italic"><b>*Error : </b>{{ $message }}</div>
            @enderror
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <button type="submit" class="btn btn-primary">Buat Akun</button>
    </form>
</div>
@endsection
