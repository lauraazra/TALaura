<nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color:#F4FFF6;">
    <div class="container">
        <a class="navbar-brand" href="/dashboard"><img src="/img/samudraLogo.png" alt="Samudra Kue" class="img-fluid"></a>
        <button class="custom-toggler navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "Dashboard") ? 'active' : ''}}" href="/dashboard">Dashboard</a>
                </li>
                @if (Auth::user()->role == 'admin')
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "Product") ? 'active' : ''}}" href="/dashboard/product">Produk</a>
                </li>
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "User") ? 'active' : ''}}" href="/dashboard/users">Akun</a>
                </li>
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "Record") ? 'active' : ''}}" href="/dashboard/record">Data Penjualan</a>
                </li>
                @endif
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "Transaction") ? 'active' : ''}}" href="{{ route('transaction.index') }}">Buat Transaksi</a>
                </li>
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "Riwayat Transaksi") ? 'active' : ''}}" href="{{ route('transactiondetails.index') }}">Riwayat Transaksi</a>
                </li>
                <li class="nav-item">
                    <a class="text-dark nav-link {{ ($title === "Transaksi Batal") ? 'active' : ''}}" href="{{ route('transactiondetails.void') }}">Transaksi Batal</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                @auth
                <li class="nav-item dropdown">
                <li>
                    <form action="/logout" method="post" class="mb-2 mb-md-0">
                        @csrf
                        <button type="submit" class="btn btn-danger">Logout</button>
                    </form>
                </li>
                </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>