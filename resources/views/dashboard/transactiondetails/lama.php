@extends('layouts.main')

<style>
    .wrapper{
        height: 30px;
        width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFF;
        border-radius: 12px;
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    }
    .wrapper span{
        width: 100%;
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        margin-bottom: 5px;
    }
    .wrapper span.num{
        font-size: 15px;
        border-right: 2px solid rgba(0,0,0,0.2);
        border-left: 2px solid rgba(0,0,0,0.2);
        pointer-events: none;
    }
    #product_list {
        position: absolute;
        background: #FFF;
        width: 100%;
        z-index: 999;
        box-shadow: 0 5px 10px rgba(0,0,0,0.2);
    }

    .sticky{
        position: sticky;
        top: 35;
        z-index: 1000;
        background-color: #F4FFF6; /* Menambahkan background color */        
    }

    #product_list:hover {
        cursor: pointer;
    }
</style>

@section('container')
    <div class="sticky">
        {{-- Judul Halaman --}}
        <div class="page-title d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-4 border-bottom">
            <h1 class="h2">{{ $title }}</h1>
        </div>
        
        {{-- Kolom Search --}}
        <div class="row justify-content-center search-column">
            <div class="col-md-6 search-column">
                <form action="/dashboard/transactiondetail">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search..." name="search" id="name">
                        <button class="btn btn-success" type="submit">Pilih</button>
                    </div>
                        <div id="product_list" style="display: none;"></div>
                </form>
            </div>
        </div>
    </div>

    {{-- Allert Success --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show justify-content-center mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Form Transaction --}}
    <div id="transactions-detail">
        {{-- card muncul ketika ada li yang ditekan --}}
        {{-- nanti data masing-masing card jadi data transaktion detail --}}
        <div id="detail_transaksi"></div>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">(ID Produk(diambil dari produk yang yang sesuai li yang ditekan))</h5>
                <h3 class="card-title">(Nama Produk(sesuai dengan produk pada li yang ditekan))</h3>
                <div class="quantity-price d-flex justify-content-between align-items-center">
                    <div class="quantity d-flex">
                        <p class="card-text mr-2">Quantity : </p> 
                        <div class="wrapper">       
                            <span class="minus">-</span>
                            <span class="num">1</span>
                            <span class="plus">+</span>
                        </div>
                    </div>
                    <div class="price d-flex">
                        <p class="card-text mr-2">(angka dari div quantitiy) x </p>
                        <div>
                            <input type="text" placeholder="Harga" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <p class="card-text mr-2"><b>Subtotal : </b></p>
                    <div>
                        <input type="text" placeholder="Harga x quantitiy" class="form-control">
                    </div>                                       
                </div>
            </div>
        </div>
        <div class="card mb-3">
            <div class="card-body">
                <h5 class="card-title">(ID Produk(diambil dari produk yang yang sesuai li yang ditekan))</h5>
                <h3 class="card-title">(Nama Produk(sesuai dengan produk pada li yang ditekan))</h3>
                <div class="quantity-price d-flex justify-content-between align-items-center">
                    <div class="quantity d-flex">
                        <p class="card-text mr-2">Quantity : </p> 
                        <div class="wrapper">       
                            <span class="minus">-</span>
                            <span class="num">1</span>
                            <span class="plus">+</span>
                        </div>
                    </div>
                    <div class="price d-flex">
                        <p class="card-text mr-2">(angka dari div quantitiy) x </p>
                        <div>
                            <input type="text" placeholder="Harga" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <p class="card-text mr-2"><b>Subtotal : </b></p>
                    <div>
                        <input type="text" placeholder="Harga x quantitiy" class="form-control">
                    </div>                                       
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer footer-expand-lg fixed-bottom">
        <div class="container text-end">
            <p><b>Total : </b>200000</p>
            <button type="submit" class="btn btn-primary">Print</button>
            <button type="submit" class="btn btn-success">Simpan</button>
        </div>
    </footer>

    <script>
        $(document).ready(function(){
            // Fungsi untuk menampilkan hasil pencarian
            function showSearchResults(data) {
                $("#product_list").html(data);
                $("#product_list").show();
            }

            // Fungsi untuk menyembunyikan hasil pencarian
            function hideSearchResults() {
                $("#product_list").html('');
                $("#product_list").hide();
            }

            // Event listener untuk input kolom pencarian
            $("#name").on('keyup', function(){
                var value = $(this).val();
                if(value !== '') { // Cek apakah nilai input tidak kosong
                    $.ajax({
                        url: "{{ route('transactiondetails.index') }}",
                        type: "GET",
                        data: {'search': value},
                        success: function(data){
                            showSearchResults(data);
                        }
                    });
                } else {
                    hideSearchResults();
                }
            });

            // Event listener untuk menangkap klik di luar div #product_list dan kolom pencarian
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#product_list').length && !$(e.target).closest('#name').length) {
                    hideSearchResults();
                }
            });
        });

        // Event listener untuk menangani klik pada setiap elemen daftar produk
        $("#product_list").on('click', '.list-group-item', function() {
            var productId = $(this).data('product-id');
            // Kirim permintaan AJAX ke endpoint yang sesuai dengan ID produk
            $.ajax({
                url: "{{ route('transactiondetails.index') }}/" + productId, // Ganti dengan endpoint yang sesuai
                type: "GET",
                success: function(data) {
                    // Tampilkan detail produk di dalam div detail_transaksi
                    $("#detail_transaksi").html(data);
                },
                error: function(xhr, status, error) {
                    console.error(error); // Handle error jika diperlukan
                }
            });
        });


        // Quantity Increment Decrement Functionality
        const plus = document.querySelector(".plus"),
        minus = document.querySelector(".minus"),
        num = document.querySelector(".num");
        let a = 1;
        plus.addEventListener("click", ()=>{
            a++;
            a = (a < 10) ? a : a;
            num.innerText = a;
        });
        minus.addEventListener("click", ()=>{
            if(a > 1){
            a--;
            a = (a < 10) ? a : a;
            num.innerText = a;
            }
        });
    </script>
@endsection