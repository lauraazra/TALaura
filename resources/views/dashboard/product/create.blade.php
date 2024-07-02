@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">Buat Produk</h1>
</div>

{{-- Kolom Input --}}
<div class="col-lg-5">
    <form method="post" action="/dashboard/product" class="mb-5">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama Produk</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required>
            @error('name')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="text" class="form-control @error('price') is-invalid @enderror" id="price" name="price" required>
            @error('price')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="text" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" required>
            @error('stok')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div id="wholesale_fields">
            <div class="wholesale-field mb-3">
                <label for="wholesale_price_1" class="form-label">Harga Grosir 1</label>
                <div class="d-flex">
                    <input type="text" class="form-control wholesale-price" name="wholesale_prices[]" required>
                    <span class="mx-2">per</span>
                    <input type="text" class="form-control quantity" name="quantities[]" placeholder="Minimum Pembelian">
                    <span class="mx-2">pcs</span>
                    <div>
                        <button type="button" class="badge bg-danger border-0 delete-wholesale" style="font-size: 10px" ;>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="add_wholesale_field" class="btn btn-success">Tambah Harga Grosir +</button>
        <button type="submit" class="btn btn-primary">Buat Produk</button>
    </form>
</div>
<script>
    // Tambah Kolom Harga Grosir
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen tombol dan form
        const addWholesaleButton = document.getElementById('add_wholesale_field');
        const wholesaleFieldsContainer = document.getElementById('wholesale_fields');

        // Event dan Div Tambahan
        addWholesaleButton.addEventListener('click', function() {
            const wholesaleCounter = wholesaleFieldsContainer.querySelectorAll('.wholesale-field').length + 1;
            const newWholesaleField = `
                    <div class="wholesale-field mb-3">
                        <label for="wholesale_price_${wholesaleCounter}" class="form-label">Harga Grosir ${wholesaleCounter}</label>
                        <div class="d-flex align-items-center">
                            <input type="text" class="form-control wholesale-price" name="wholesale_prices[]" required>
                            <span class="mx-2">per</span>
                            <input type="text" class="form-control quantity" name="quantities[]" placeholder="Minimum Pembelian" required>
                            <span class="mx-2">pcs</span>
                            <button type="button" class="badge bg-danger border-0 delete-wholesale" style="font-size: 10px";>
                                Delete
                            </button>
                        </div>
                    </div>
                `;
            wholesaleFieldsContainer.insertAdjacentHTML('beforeend', newWholesaleField);
            feather.replace(); // Panggil kembali fungsi feather.replace() setelah menambahkan kolom baru
        });

        // Hapus Div
        wholesaleFieldsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-wholesale')) {
                event.target.closest('.wholesale-field').remove();
                updateWholesaleCounters();
            }
        });

        // Nomor kolom dan label
        function updateWholesaleCounters() {
            const wholesaleFields = wholesaleFieldsContainer.querySelectorAll('.wholesale-field');
            wholesaleFields.forEach((field, index) => {
                const counterLabel = field.querySelector('label');
                counterLabel.textContent = `Harga Grosir ${index + 1}`;
            });
        }
    });
</script>
@endsection