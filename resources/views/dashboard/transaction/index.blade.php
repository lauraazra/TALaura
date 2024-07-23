@extends('layouts.main')

<style>
    .wrapper {
        height: 30px;
        width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #FFF;
        border-radius: 12px;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .wrapper span {
        width: 100%;
        text-align: center;
        font-size: 20px;
        font-weight: 600;
        cursor: pointer;
        user-select: none;
        margin-bottom: 5px;
    }

    .wrapper span.num {
        font-size: 15px;
        border-right: 2px solid rgba(0, 0, 0, 0.2);
        border-left: 2px solid rgba(0, 0, 0, 0.2);
        pointer-events: none;
    }

    #product_list {
        position: absolute;
        background: #FFF;
        width: 95%;
        z-index: 999;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    .sticky {
        position: sticky;
        top: 35;
        z-index: 1;
        background-color: #F4FFF6;
        padding-top: 1vh;
        /* Menambahkan background color */
    }

    #transactions-detail {
        padding-top: 3vh;
        padding-bottom: 13vh;
    }

    #overlay {
        visibility: hidden;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.8);
        /* Black background with transparency */
        z-index: 3;
        /* Ensure it's on top of other elements */
    }

    #spinner {
        visibility: hidden;
        width: 80px;
        height: 80px;

        border: 5px solid #f3f3f3;
        border-top: 6px solid #28a745;
        border-radius: 100%;

        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);

        animation: spin 1s infinite linear;
        z-index: 4;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    /* Media query for screen sizes 400px and below */
    @media screen and (max-width: 500px) {
        #spinner {
            left: 42%;
        }

        #product_list {
            width: 93%;
        }
    }

    @media screen and (max-width: 400px) {
        #transactions-detail {
            padding-bottom: 14vh;
        }
    }

    #modal-name {
        visibility: hidden;
        position: fixed;
        z-index: 5;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
    }

    #spinner.show,
    #overlay.show,
    #modal-name.show {
        visibility: visible;
    }
</style>

@section('container')
<div class="sticky border-bottom">
    {{-- Judul Halaman --}}
    <div class="page-title d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-4 border-bottom">
        <h1 class="h2">Buat Transaksi</h1>
    </div>

    {{-- Kolom Search --}}
    <div class="row justify-content-center search-column mb-2">
        <div class="col-md-6 search-column">
            <form class="form-produk">
                @csrf
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Tambahkan Produk..." name="search" id="name">
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
    <div id="container-cart">
        <div id="empty-cart" class="d-flex justify-content-center align-items-center fs-2 my-5">Tidak ada Transaksi</div>
    </div>
</div>

<footer class="footer footer-expand-lg fixed-bottom" style="background-color: #F4FFF6; z-index: 2;">
    <div class="container border-top text-end py-3">
        <p><b>Total : </b><span id="total-pay">Rp. 0</span></p>
        <button id="clear" type="submit" class="btn btn-primary mr-2" disabled>Clear Data</button>
        <button id="simpan" type="submit" class="btn btn-success" disabled>Simpan</button>
    </div>
</footer>

<div id="overlay"></div>
<div id="spinner"></div>
<div id="modal-name">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Input Nama Pembeli</h5>
                <button type="button" class="btn-close close-modal" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" class="form-control" id="buyer-name" placeholder="Nama Pembeli...">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary close-modal" data-bs-dismiss="modal">Close</button>
                <button id="save-buyer-name" type="button" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

<script>
    var cart = new Map();

    $(document).ready(function() {
        var buyerName = 'Pembeli';
        $("#overlay, #modal-name").addClass('show');

        $("#buyer-name").on('keyup', function(e) {
            if (e.key === 'Enter') {
                $("#save-buyer-name").click();
            }
        });

        $("#save-buyer-name").on('click', function() {
            var inputBuyerName = $("#buyer-name").val();
            if (inputBuyerName) {
                buyerName = inputBuyerName;
                $("#overlay, #modal-name").removeClass('show');
                $("#buyer-name").val('');
            }
        });

        // Fungsi untuk menampilkan hasil pencarian
        function showSearchResults(data) {
            $("#product_list").html(data).show();
        }

        // Fungsi untuk menyembunyikan hasil pencarian
        function hideSearchResults() {
            $("#product_list").html('').hide();
        }

        // teknik debounce
        var delayTimer;
        // Event listener untuk input kolom pencarian
        $("#name").on('keyup', function() {
            var value = $(this).val();
            if (value.length < 3) { // Cek apakah nilai input tidak kosong & lebih dari 2 karakter
                return hideSearchResults();
            }

            clearTimeout(delayTimer); // Menghapus timer yang telah dijalankan

            delayTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('transaction.index') }}",
                    type: "GET",
                    data: {
                        search: value
                    },
                    success: function(data) {
                        showSearchResults(data);
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error(`Error:  ${textStatus}`);
                        showSearchResults(`<li class="list-group-item">${errorThrown}</li>`);
                    }
                });
            }, 1000);
        });

        // Event listener untuk menangkap klik di luar div #product_list dan kolom pencarian
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#product_list').length && !$(e.target).closest('#name').length) {
                hideSearchResults();
            }
        });

        $('.form-produk').on('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
            }
        });

        $('#simpan').on('click', function() {
            $.ajax({
                url: "{{ route('transaction.store') }}",
                type: "POST",
                data: {
                    cart: Object.fromEntries(cart),
                    subtotal: parseFloat($('#total-pay').text().replace(/[^\d.-]+/g, '')),
                    buyer_name: buyerName,
                },
                beforeSend: function() {
                    $('#spinner, #overlay').addClass('show');
                },
                success: function(data) {
                    setTimeout(() => {
                        // alert('Transaksi Berhasil');
                        $('#spinner, #overlay').removeClass('show');
                        window.location.href = "{{ route('transaction.success') }}";
                    }, 3000);
                },
                error: function(xhr, textStatus, errorThrown) {
                    setTimeout(() => {
                        $('#spinner, #overlay').removeClass('show');
                        console.error(`Error: ${textStatus}`);
                        alert(errorThrown);
                    }, 3000);
                }
            })
        });

        $('#clear').on('click', function() {
            if (!confirm("Are you sure you want to clear transactoin?")) {
                return;
            }
            location.reload();
        });

        $('.close-modal').on('click', function() {
            $('#overlay, #modal-name').removeClass('show');
        });

        $('#clear, #simpan').prop('disabled', true);
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    function pilihProduk(id) {
        if (cart.get(`product_${id}`)) {
            return alert('Produk sudah ada di keranjang');
        }

        $.ajax({
            url: `{{ route('product.show') }}`,
            type: "GET",
            data: {
                id: id
            },
            success: function(data) {
                if (!data.stok) {
                    return alert('Stok produk habis');
                }
                data.qty = 1;
                data.grosir_price = data.price;
                data.subtotal = data.qty * data.price;
                data.new_stok = data.stok - 1;
                cart.set(`product_${data.id}`, data);
                var containerCart = addToCart(data);
                $('#container-cart').append(containerCart);
                $('#total-pay').text(formatRupiah(sumSubtotal(cart)));
                $('#empty-cart').html('').removeAttr('class').hide();
                $("#product_list").html('').hide();
                $("#name").val('');
                $("#simpan, #clear").prop('disabled', false);
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error(`Error: ${textStatus}`);
                alert(errorThrown);
            }
        });
    }

    function addToCart(product) {
        var productDiv = $(`<div id="product_${product.id}" class="card mb-3"></div>`);
        var cardBody = $(`<div class="card-body"></div>`);
        var titleId = $(`<h5 class="card-title font-weight-bold">ID Produk: ${product.id}</h5>`);
        var titleNama = $(`<h3 class="card-title font-weight-bold">Produk: ${product.name}</h3>`);
        var quantityPrice = $(`<div class="quantity-price d-md-flex justify-content-between align-items-center"></div>`);
        var quantityDiv = $(`<div class="quantity d-flex justify-content-between mb-2"></div>`);
        var quantityText = $(`<p class="card-text mr-2">Quantity : </p>`);
        var wrapper = $(`<div class="wrapper"></div>`);
        var minusSpan = $(`<span class="product_${product.id}_minus">-</span>`);
        var numSpan = $(`<span class="product_${product.id}_num">${product.qty}</span>`);
        var plusSpan = $(`<span class="product_${product.id}_plus">+</span>`);
        var priceDiv = $(`<div class="price d-flex justify-content-between"></div>`);
        var priceText = $(`<p class="card-text mr-2 mt-1 mb-0"><span class="product_${product.id}_qty">${product.qty}</span> x </p>`);
        var divContainerHarga = $(`<div></div>`);
        var priceInput = $(`<input id="product_${product.id}_price" type="text" placeholder="Harga" class="form-control" value="${formatRupiah(product.price)}" readonly>`);
        var subtotalDiv = $(`<div class="d-md-flex justify-content-between"></div>`);
        var sisaStok = $(`<h6 class="pt-md-0 pt-2 d-flex justify-content-between">Sisa Stok :<b class="px-2 product_${product.id}_stok">${product.new_stok}</b></h6>`);
        var subtotalContainer = $(`<div class="price d-flex justify-content-between"></div>`);
        var subtotalText = $(`<p class="card-text mr-2 mt-2 mb-0"><b>Subtotal : </b></p>`);
        var divContainerSubtotal = $(`<div></div>`);
        var subtotalInput = $(`<input id="product_${product.id}_subtotal" type="text" placeholder="Harga x quantity" class="form-control" value="${formatRupiah(product.qty * product.price)}" readonly>`);

        // Appending child elements
        wrapper.append(minusSpan, numSpan, plusSpan);
        quantityDiv.append(quantityText, wrapper);
        priceDiv.append(priceText, divContainerHarga);
        divContainerHarga.append(priceInput);
        subtotalDiv.append(sisaStok, subtotalContainer);
        subtotalContainer.append(subtotalText, divContainerSubtotal);
        divContainerSubtotal.append(subtotalInput);
        quantityPrice.append(quantityDiv, priceDiv);
        cardBody.append(titleId, titleNama, quantityPrice, subtotalDiv);
        productDiv.append(cardBody);

        // Event listener untuk tombol + dan -
        plusSpan.on('click', function() {
            var qty = parseInt($(`.product_${product.id}_qty`).text());
            if (qty >= product.stok) {
                return alert(`Stok habis, sisa stok: ${product.stok}`);
            }
            countProduct('plus', product, qty);
        });
        minusSpan.on('click', function() {
            var qty = parseInt($(`.product_${product.id}_qty`).text());
            if (qty > 1) {
                return countProduct('minus', product, qty);
            }
            if (!confirm("Are you sure you want to remove this item?")) {
                return;
            }
            $(`#product_${product.id}`).off("click").remove();
            cart.delete(`product_${product.id}`);
            $('#total-pay').text(formatRupiah(sumSubtotal(cart)));
            if ($('#container-cart').children().length === 1) {
                $('#empty-cart').html('Transaction is empty')
                    .addClass('d-flex justify-content-center align-items-center fs-2 my-5')
                    .show();
                $("#simpan, #clear").prop('disabled', true);
            }
        });
        numSpan.on('click', function() {
            var inputQty = prompt('Masukan jumlah quantity:');
            if (!inputQty || !/^\d+$/.test(inputQty) || inputQty === '0') {
                return alert(!inputQty ? 'Quantity tidak boleh kosong' : 'Please enter a valid number.');
            }
            inputQty = parseInt(inputQty);
            if (inputQty > product.stok) {
                return alert(`Stok habis, sisa stok: ${product.stok}`);
            }
            $(this).text(inputQty);
            countProduct('direct', product, inputQty);
        });

        return productDiv;
    }

    function countProduct(type = 'direct', product, qty) {
        let diffQty = product.qty - qty;
        let newQty = type === 'plus' ? qty + 1 : type === 'minus' ? qty - 1 : qty;
        let newStok = type === 'plus' ? product.new_stok - 1 : type === 'minus' ? product.new_stok + 1 : product.new_stok + diffQty;

        $(`.product_${product.id}_qty, .product_${product.id}_num`).text(newQty);
        $(`.product_${product.id}_stok`).text(newStok);
        product.qty = newQty;
        product.new_stok = newStok;

        let wholesale = product.wholesale;
        let price = product.price;
        let grosirPrice = product.grosir_price;

        let filterGrosirPrice = filterWholeSale(newQty, wholesale);
        let newGrosirPrice = filterGrosirPrice ? filterGrosirPrice.price / filterGrosirPrice.quantity : price;

        if (newGrosirPrice !== grosirPrice) {
            grosirPrice = newGrosirPrice;
            alert(filterGrosirPrice ? `Harga grosir ${filterGrosirPrice.price} per ${filterGrosirPrice.quantity} pcs` : 'Kembali ke harga awal per pcs');
        }

        let subtotal = newQty * grosirPrice;

        $(`#product_${product.id}_price`).val(formatRupiah(grosirPrice));
        $(`#product_${product.id}_subtotal`).val(formatRupiah(subtotal));
        product.grosir_price = grosirPrice;
        product.subtotal = subtotal;

        cart.set(`product_${product.id}`, product);
        $('#total-pay').text(formatRupiah(sumSubtotal(cart)));
    }

    function filterWholeSale(input, wholesale) {
        let findWholesale = wholesale.filter(item => input >= item.quantity);

        if (!findWholesale.length) {
            return null;
        }

        return findWholesale.reduce((minItem, currentItem) => {
            const minTotalValue = minItem.price / minItem.quantity;
            const currentTotalValue = currentItem.price / currentItem.quantity;
            return minTotalValue < currentTotalValue ? minItem : currentItem;
        });
    }

    function sumSubtotal(hashMap) {
        let totalPrice = 0;
        for (const [key, value] of hashMap) {
            totalPrice += value.subtotal;
        }
        return totalPrice;
    }

    function formatRupiah(amount) {
        return `Rp ${amount}`;
    }
</script>
@endsection