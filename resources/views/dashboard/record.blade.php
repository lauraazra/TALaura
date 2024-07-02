@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Data Penjualan</h1>
</div>

{{-- Tabel Omset Perbulan --}}
<div class="container mt-2">
    <h2>Tabel Omset Perbulan</h2>
    {{-- Form untuk filter omset perbulan --}}
    <form action="/dashboard/record" method="GET">
        <div class="input-group mb-3">
            <select name="filter_month" class="form-select">
                <option value="">Pilih Bulan</option>
                @foreach ($availableMonths as $key => $month)
                    @if ($monthlySalesMonths->contains($key))
                        <option value="{{ $key }}" {{ request('filter_month') == $key ? 'selected' : '' }}>{{ $month }}</option>
                    @endif
                @endforeach
            </select>
            <select name="filter_year" class="form-select">
                <option value="">Pilih Tahun</option>
                @foreach ($availableYears as $year)
                    @if ($monthlySalesYears->contains($year))
                        <option value="{{ $year }}" {{ request('filter_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endif
                @endforeach
            </select>
            <button class="btn btn-success" type="submit">Filter</button>
        </div>
    </form>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Bulan</th>
                <th scope="col">Tahun</th>
                <th scope="col">Total Omset</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthlySales as $monthlySale)
            <tr>
                <td>{{ \Carbon\Carbon::parse($monthlySale->month . '/1')->format('F') }}</td>
                <td>{{ $monthlySale->year }}</td>
                <td>Rp {{ number_format($monthlySale->total_sales, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $monthlySales->links() }} {{-- Pagination link for monthlySales --}}
</div>

{{-- Tabel Omset Per Kasir atau User --}}
<div class="container mt-5">
    <h2>Tabel Omset Per Akun</h2>
    {{-- Form untuk filter omset per kasir atau user --}}
    <form action="/dashboard/record" method="GET">
        <div class="input-group mb-3">
            <select name="filter_user" class="form-select">
                <option value="">Pilih Kasir/User</option>
                @foreach ($availableUsers as $user)
                    <option value="{{ $user->id }}" {{ request('filter_user') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            <select name="filter_month_user" class="form-select">
                <option value="">Pilih Bulan</option>
                @foreach ($availableMonths as $key => $month)
                    @if ($monthlySalesByUserMonths->contains($key))
                        <option value="{{ $key }}" {{ request('filter_month_user') == $key ? 'selected' : '' }}>{{ $month }}</option>
                    @endif
                @endforeach
            </select>
            <select name="filter_year_user" class="form-select">
                <option value="">Pilih Tahun</option>
                @foreach ($availableYears as $year)
                    @if ($monthlySalesByUserYears->contains($year))
                        <option value="{{ $year }}" {{ request('filter_year_user') == $year ? 'selected' : '' }}>{{ $year }}</option>
                    @endif
                @endforeach
            </select>
            <button class="btn btn-success" type="submit">Filter</button>
        </div>
    </form>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Kasir</th>
                <th scope="col">Bulan</th>
                <th scope="col">Tahun</th>
                <th scope="col">Total Omset</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthlySalesByUser as $monthlySale)
            <tr>
                <td>{{ $monthlySale->kasir }}</td>
                <td>{{ \Carbon\Carbon::parse($monthlySale->month . '/1')->format('F') }}</td>
                <td>{{ $monthlySale->year }}</td>
                <td>Rp {{ number_format($monthlySale->total_sales, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $monthlySalesByUser->links() }} {{-- Pagination link for monthlySalesByUser --}}
</div>

{{-- Tabel Daftar Transaksi --}}
<div class="container mt-5">
    <h2>Tabel Data Transaksi</h2>
    <form action="/dashboard/record" method="GET">
        <div class="input-group mb-3">
            <input type="text" class="form-control" placeholder="Cari Data..." name="search" value="{{ request('search') }}">
            <select name="filter" class="form-select">
                <option value="">Sepanjang Waktu</option>
                <option value="this_month" {{ request('filter') == 'this_month' ? 'selected' : '' }}>Bulan Ini ({{ strftime('%B') }})</option>
                <option value="last_month" {{ request('filter') == 'last_month' ? 'selected' : '' }}>1 Bulan Lalu ({{ strftime('%B', strtotime('-1 month')) }})</option>
                <option value="two_months_ago" {{ request('filter') == 'two_months_ago' ? 'selected' : '' }}>2 Bulan Lalu ({{ strftime('%B', strtotime('-2 months')) }})</option>
            </select>
            <button class="btn btn-success" type="submit">Cari</button>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="semua" value="semua" {{ request('status') == 'semua' ? 'checked' : '' }}>
            <label class="form-check-label" for="semua">Semua</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="gagal" value="gagal" {{ request('status') == 'gagal' ? 'checked' : '' }}>
            <label class="form-check-label" for="gagal">Gagal</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="status" id="berhasil" value="berhasil" {{ request('status') == 'berhasil' ? 'checked' : '' }}>
            <label class="form-check-label" for="berhasil">Berhasil</label>
        </div>
    </form>
    <table class="table">
        <thead class="thead-dark">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Pembeli</th>
                <th scope="col">Kasir</th>
                <th scope="col">Tanggal Pembelian</th>
                <th scope="col">Status</th>
                <th scope="col">Total Transaksi</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $currentPage = $transactions->currentPage();
                $perPage = $transactions->perPage();
                $startNumber = ($currentPage - 1) * $perPage + 1;
            @endphp
            @foreach ($transactions as $transaction)
            <tr>
                <td>{{ $startNumber++ }}</td>
                <td>{{ $transaction->buyer_name }}</td>
                <td>{{ $transaction->user->name }}</td>
                <td>{{ \Carbon\Carbon::parse($transaction->transaction_time)->format('d-m-Y') }}</td>
                <td>{{ $transaction->void == 1 ? 'gagal' : 'berhasil' }}</td>
                <td>Rp {{ number_format($transaction->total_price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $transactions->links() }} {{-- Pagination Halaman --}}
</div>

@endsection