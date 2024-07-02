@extends('layouts.main')

@section('container')
{{-- Judul Halaman --}}
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom" style="padding-top: 8vh;">
    <h1 class="h2">{{ $title }}</h1>
</div>

{{-- Form Edit Produk --}}
<div class="col-lg-5">
    <form method="post" action="/dashboard/product/{{ $product->id }}" class="mb-5">
        @method('put')
        @csrf
        {{-- Input Nama --}}
        <div class="mb-3">
            <label for="name" class="form-label">Nama Produk</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" required autofocus value="{{ old('name', $product->name) }}">
            @error('name')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        {{-- Input Harga --}}
        <div class="mb-3">
            <label for="price" class="form-label">Harga</label>
            <input type="text" class="form-control @error('price') is-invalid @enderror" id="price" name="price" required value="{{ old('price', $product->price) }}">
            @error('price')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ "Harga Tidak Boleh Kurang dari 1" }}
            </div>
            @enderror
        </div>
        {{-- Input Stok --}}
        <div class="mb-3">
            <label for="stok" class="form-label">Stok</label>
            <input type="text" class="form-control @error('stok') is-invalid @enderror" id="stok" name="stok" min="0" required value="{{ old('stok', $product->stok) }}">
            @error('stok')
            <div id="validationServer04Feedback" class="invalid-feedback">
                {{ "Stok Tidak Boleh Kurang dari 0" }}
            </div>
            @enderror
        </div>
        {{-- Input Harga Grosir --}}
        <div id="wholesale_fields">
            @foreach ($wholesales as $key => $wholesale)
            <div class="wholesale-field mb-3">
                <label for="wholesale_price_{{ $key }}" class="form-label">Harga Grosir {{ $key + 1 }}</label>
                <div class="d-flex">
                    {{-- Harga Grosir --}}
                    <div class="wholesale_price">
                        <input type="text" class="form-control @error('wholesale_prices.' . $wholesale->id) is-invalid @enderror" id="wholesale_prices_{{ $key }}" name="wholesale_prices[{{ $wholesale->id }}]" required value="{{ old('wholesale_prices.' . $wholesale->id, $wholesale->price) }}" data-key="{{ $key }}">
                        @error('wholesale_prices.' . $wholesale->id)
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            {{ "Harga Grosir Tidak Boleh Kurang dari 1" }}
                        </div>
                        @enderror
                    </div>
                    <span class="mx-2">per</span>
                    {{-- Minimum Pembelian --}}
                    <div class="quantity">
                        <input type="text" class="form-control quantity @error('quantities.' . $wholesale->id) is-invalid @enderror" id="quantities_{{ $key }}" name="quantities[{{ $wholesale->id }}]" required value="{{ old('quantities.' . $wholesale->id, $wholesale->quantity) }}" min="0">
                        @error('quantities.' . $wholesale->id)
                        <div id="validationServer04Feedback" class="invalid-feedback">
                            {{ "Minimum Pembelian Tidak Boleh Kurang dari 1" }}
                        </div>
                        @enderror
                    </div>
                    <span class="mx-2">pcs</span>
                    <div>
                        <button type="button" class="badge bg-danger border-0 delete-wholesale" style="font-size: 10px" ;>
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-success" id="add_wholesale_field">Tambah Harga Grosir +</button>
        <button type="submit" class="btn btn-primary">Update Produk</button>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addWholesaleButton = document.getElementById('add_wholesale_field');
        const wholesaleFieldsContainer = document.getElementById('wholesale_fields');

        // Menambah Input Harga Grosir
        addWholesaleButton.addEventListener('click', function() {
            const wholesaleCounter = wholesaleFieldsContainer.querySelectorAll('.wholesale-field').length + 1;
            const newWholesaleField = `
                    <div class="wholesale-field mb-3">
                        <label for="wholesale_price_${wholesaleCounter}" class="form-label">Harga Grosir ${wholesaleCounter}</label>
                        <div class="d-flex">
                            <input type="text" class="form-control wholesale-price" name="wholesale_prices[]" required>
                            <span class="mx-2">per</span>
                            <input type="text" class="form-control quantity" name="quantities[]" placeholder="Minimum Purchase Quantity" required>
                            <span class="mx-2">pcs</span>
                            <div>
                                <button type="button" class="badge bg-danger border-0 delete-wholesale" style="font-size: 10px";>Delete</button>
                            </div>
                        </div>
                    </div>
                `;
            wholesaleFieldsContainer.insertAdjacentHTML('beforeend', newWholesaleField);
        });

        // Menghapus Input Harga Grosir
        wholesaleFieldsContainer.addEventListener('click', function(event) {
            if (event.target.classList.contains('delete-wholesale')) {
                event.target.closest('.wholesale-field').remove();
                updateWholesaleCounters();
                console.log('Price Deleted');
            }
        });

        //  Memperbarui angka nomor setiap input harga grosir yang dibu
        function updateWholesaleCounters() {
            const wholesaleFields = wholesaleFieldsContainer.querySelectorAll('.wholesale-field');
            wholesaleFields.forEach((field, index) => {
                const counterLabels = field.querySelectorAll('label');
                const priceLabel = counterLabels[0];

                priceLabel.textContent = `Harga Grosir ${index + 1}`;
            });
        }
    });
</script>
@endsection