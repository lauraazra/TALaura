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
        padding-bottom: 8vh;
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

    @media screen and (max-width: 766px) {
        #transactions-detail {
            padding-bottom: 14vh;
        }
    }

    @media screen and (max-width: 400px) {
        #transactions-detail {
            padding-bottom: 15vh;
        }
    }

    #spinner.show,
    #overlay.show {
        visibility: visible;
    }
</style>

@section('container')
<div class="sticky border-bottom">
    {{-- Judul Halaman --}}
    <div class="page-title d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 mt-4 border-bottom">
        <h1 class="h2">Edit Transaksi</h1>
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

{{-- Form Transaction --}}
<div id="transactions-detail">
    <div id="container-cart">
        <div class="card mb-3">
            <div class="card-body d-flex justify-content-between flex-column flex-md-row">
                <div class="card-wrapper">
                    <h5 class="card-title font-weight-bold">ID Transaction: {{ $transaction->id }}</h5>
                    <h5 class="card-title">Pembeli: {{ $transaction->buyer_name }}</h5>
                    <h5 class="card-title mb-3 mb-md-0">Pembuat Nota: {{ $transaction->user->name }}</h5>
                </div>
                <div class="card-wrapper">
                    <h5 class="card-title font-weight-bold">{{ $transaction->transaction_time }}</h5>
                    <h5 class="card-title">Total Item: <span id="total-item">{{ $transaction->total_item }}</span></h5>
                    <h5 class="card-title mb-0">Transaction: <span id="total-pay">Rp {{ $transaction->total_price }}</span></h5>
                </div>
            </div>
        </div>
        @foreach ($transaction->details as $details)
        <div id="product_{{ $details->product_id }}" class="card mb-3">
            <div class="card-body">
                <h5 class="card-title font-weight-bold mb-1">ID Produk: {{ $details->product_id }}</h5>
                <h3 class="card-title font-weight-bold">Produk: {{ $details->product->name }}</h3>
                <div class="quantity-price d-md-flex justify-content-between align-items-center">
                    <div class="quantity d-flex justify-content-between mb-2">
                        <p class="card-text mr-2">Quantity : </p>
                        <div class="wrapper">
                            <span class="product_{{ $details->product_id }}_minus" onclick="minusQty({{ $details->product_id }})">-</span>
                            <span class="product_{{ $details->product_id }}_num" onclick="numQty({{ $details->product_id }})">{{ $details->quantity }}</span>
                            <span class=" product_{{ $details->product_id }}_plus" onclick="plusQty({{ $details->product_id }})">+</span>
                        </div>
                    </div>
                    <div class="price d-flex justify-content-between">
                        <p class="card-text mr-2 mt-1 mb-0"><span class="product_{{ $details->product_id }}_qty">{{ $details->quantity }}</span> x </p>
                        <div>
                            <input id="product_{{ $details->product_id }}_price" type="text" placeholder="Harga" class="form-control" value="Rp {{ $details->price }}" readonly>
                        </div>
                    </div>
                </div>
                <div class="d-md-flex justify-content-between">
                    <h6 class="pt-md-0 pt-2 d-flex justify-content-between">Sisa stok: <b class="px-2 product_{{ $details->product_id }}_stok">{{ $details->product->stok }}</b></h6>
                    <div class="price d-flex justify-content-between">
                        <p class="card-text mr-2 mt-2 mb-0"><b>Subtotal : </b></p>
                        <div>
                            <input id="product_{{ $details->product_id }}_subtotal" type="text" placeholder="Harga x quantitiy" class="form-control" value="Rp {{ $details->subtotal }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<footer class="footer footer-expand-lg fixed-bottom" style="background-color: #F4FFF6; z-index: 2;">
    <div class="container border-top text-end py-3 d-flex flex-md-row flex-column gap-3 justify-content-between">
        <div>
            @if ($transaction->void)
            <form action="{{ route('transaction.unvoid', ['transaction' => $transaction->id]) }}" method="post"  class="mb-0">
                @method('post')
                @csrf
                <button class="btn btn-success" onclick="return confirm('Anda yakin ingin mengembalikan transaksi?')">Unvoid Transaction</button>
            </form>
            @else
            <span class="h5 font-weight-bold mr-2">Invoice</span>
            <a href="{{ route('transaction.show_print', ['transaction' => $transaction->id]) }}" class="btn btn-success mr-1">Show</a>
            <a href="{{ route('transaction.download_print', ['transaction' => $transaction->id]) }}" class="btn btn-success mr-1">Download</a>
            <button id="print" class="btn btn-primary">Print</button>
            @endif
        </div>
        <div class="d-flex justify-content-end gap-2">
            <form id="form-clear" class="mb-0">
                <button id="clear" class="btn btn-primary" disabled>Cancel</button>
            </form>
            @if (!$transaction->void)
            <form action="{{ route('transaction.void', ['transaction' => $transaction->id]) }}" method="post" class="mb-0">
                @method('post')
                @csrf
                <button class="btn btn-danger" onclick="return confirm('Anda yakin ingin void transaksi?')">Void Transaction</button>
            </form>
            @endif
            <form id="form-save" class="mb-0">
                <button id="simpan" class="btn btn-success" disabled>Simpan</button>
            </form>
        </div>
    </div>
</footer>

<div id="overlay"></div>
<div id="spinner"></div>

<script src="https://printjs-4de6.kxcdn.com/print.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://printjs-4de6.kxcdn.com/print.min.css">

<script>
    var cart = new Map();

    $(document).ready(function() {
        $.ajax({
            url: "{{ route('transaction.get_detail', ['transaction' => $transaction->id]) }}",
            type: "GET",
            success: function(data) {
                const details = data.details;
                if (!details) {
                    alert('Tidak dapat menemukan detail transaksi');
                    location.reload();
                    return;
                }
                details.forEach(detail => {
                    detail.new_qty = detail.quantity;
                    detail.new_stok = detail.product.stok;
                    detail.grosir_price = detail.product.price;

                    cart.set(`product_${detail.product_id}`, detail);
                });
            },
            error: function(xhr, textStatus, errorThrown) {
                console.error(`Error: ${textStatus}`);
                alert(errorThrown);
                location.reload();
            }
        });

        $('#print').on('click', function() {
            @if ($print_method == 'v2')
            window.location.href = "{{ route('transaction.print_v2', ['transaction' => $transaction->id]) }}";
            @else
            $.ajax({
                url: "{{ route('transaction.print_url', ['transaction' => $transaction->id]) }}",
                type: "GET",
                beforeSend: function() {
                    $('#spinner, #overlay').addClass('show');
                },
                success: function(response) {
                    if (!response.url) {
                        $('#spinner, #overlay').removeClass('show');
                        return alert('Tidak dapat menemukan invoice');
                    }
                    setTimeout(() => {
                        $('#spinner, #overlay').removeClass('show');
                        printJS(response.url);
                    }, 3000);
                },
                error: function(xhr, textStatus, errorThrown) {
                    setTimeout(() => {
                        $('#spinner, #overlay').removeClass('show');
                        console.error(`Error: ${textStatus}`);
                        alert(errorThrown);
                    }, 3000);
                }
            });
            @endif
        });

        $('#simpan').on('click', function() {
            $.ajax({
                url: "{{ route('transaction.update', ['transaction' => $transaction->id]) }}",
                type: "POST",
                data: {
                    cart: Object.fromEntries(cart),
                    subtotal: parseFloat($('#total-pay').text().replace(/[^\d.-]+/g, '')),
                },
                beforeSend: function() {
                    console.log(cart);
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

        $('#form-clear, #form-save').on('submit', function(e) {
            e.preventDefault();
        });

        $('#clear').on('click', function() {
            if (!confirm("Apakah anda yakin ingin membatalkan perubahan?")) {
                return;
            }
            location.reload();
        });

        function showSearchResults(data) {
            $("#product_list").html(data).show();
        }

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
                const product = {};
                product.product_id = data.id;
                product.product = data;
                product.quantity = 0;
                product.new_qty = 1;
                product.new_stok = data.stok - product.new_qty;
                product.price = data.price;
                product.grosir_price = data.price;
                product.subtotal = data.price * product.new_qty;
                cart.set(`product_${data.id}`, product);
                var containerCart = addToCart(product);
                $('#container-cart').append(containerCart);
                $('#total-pay').text(formatRupiah(sumSubtotal(cart)));
                $('#total-item').text(parseInt(cart.size));
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
        var productDiv = $(`<div id="product_${product.product.id}" class="card mb-3"></div>`);
        var cardBody = $(`<div class="card-body"></div>`);
        var titleId = $(`<h5 class="card-title font-weight-bold mb-1">ID Produk: ${product.product.id}</h5>`);
        var titleNama = $(`<h3 class="card-title font-weight-bold">Produk: ${product.product.name}</h3>`);
        var quantityPrice = $(`<div class="quantity-price d-md-flex justify-content-between align-items-center"></div>`);
        var quantityDiv = $(`<div class="quantity d-flex justify-content-between mb-2"></div>`);
        var quantityText = $(`<p class="card-text mr-2">Quantity : </p>`);
        var wrapper = $(`<div class="wrapper"></div>`);
        var minusSpan = $(`<span class="product_${product.product.id}_minus" onclick="minusQty(${product.product.id})">-</span>`);
        var numSpan = $(`<span class="product_${product.product.id}_num" onclick="numQty(${product.product.id})">${product.new_qty}</span>`);
        var plusSpan = $(`<span class="product_${product.product.id}_plus" onclick="plusQty(${product.product.id})">+</span>`);
        var priceDiv = $(`<div class="price d-flex justify-content-between"></div>`);
        var priceText = $(`<p class="card-text mr-2 mt-1 mb-0"><span class="product_${product.product.id}_qty">${product.new_qty}</span> x </p>`);
        var divContainerHarga = $(`<div></div>`);
        var priceInput = $(`<input id="product_${product.product.id}_price" type="text" placeholder="Harga" class="form-control" value="${formatRupiah(product.price)}" readonly>`);
        var subtotalDiv = $(`<div class="d-md-flex justify-content-between"></div>`);
        var sisaStok = $(`<h6 class="pt-md-0 pt-2 d-flex justify-content-between">Sisa Stok: <b class="px-2 product_${product.product.id}_stok">${product.new_stok}</b></h6>`);
        var subtotalContainer = $(`<div class="price d-flex justify-content-between"></div>`);
        var subtotalText = $(`<p class="card-text mr-2 mt-2 mb-0"><b>Subtotal : </b></p>`);
        var divContainerSubtotal = $(`<div></div>`);
        var subtotalInput = $(`<input id="product_${product.product.id}_subtotal" type="text" placeholder="Harga x quantity" class="form-control" value="${formatRupiah(product.subtotal)}" readonly>`);

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

        return productDiv;
    }

    function plusQty(id) {
        const detail = cart.get(`product_${id}`);
        const qty = parseInt($(`.product_${id}_qty`).text());
        const stok = detail.product.stok + detail.quantity;
        if (qty >= stok) {
            return alert(`Stok habis, sisa stok: ${detail.product.stok} - quantity asal: ${detail.quantity} pcs`);
        }
        countProduct('plus', detail, qty);
    }

    function minusQty(id) {
        const detail = cart.get(`product_${id}`);
        const qty = parseInt($(`.product_${id}_qty`).text());
        if (qty > 1) {
            return countProduct('minus', detail, qty);
        }
        if (!confirm("Are you sure you want to remove this item?")) {
            return;
        }
        if (cart.size === 1) {
            return alert(`Tidak bisa menghapus transaksi, silahkan void`);
        }
        $(`#product_${id}`).off("click").remove();
        cart.delete(`product_${id}`);
        $('#total-pay').text(formatRupiah(sumSubtotal(cart)));
        $('#total-item').text(parseInt(cart.size));
    }

    function numQty(id) {
        const detail = cart.get(`product_${id}`);
        const stok = detail.product.stok + detail.quantity;
        var inputQty = prompt('Masukan jumlah quantity:');
        if (!inputQty || !/^\d+$/.test(inputQty) || inputQty === '0') {
            return alert(!inputQty ? 'Quantity tidak boleh kosong' : 'Please enter a valid number.');
        }
        inputQty = parseInt(inputQty);
        if (inputQty > stok) {
            return alert(`Stok habis, sisa stok: ${detail.product.stok} - quantity asal: ${detail.quantity} pcs`);
        }
        $(`.product_${id}_num`).text(inputQty);
        countProduct('direct', detail, inputQty);
    }

    function countProduct(type = 'direct', product, qty) {
        let diffQty = product.new_qty - qty;
        let newQty = type === 'plus' ? qty + 1 : type === 'minus' ? qty - 1 : qty;
        let newStok = type === 'plus' ? product.new_stok - 1 : type === 'minus' ? product.new_stok + 1 : product.new_stok + diffQty;

        $(`.product_${product.product_id}_qty, .product_${product.product_id}_num`).text(newQty);
        $(`.product_${product.product_id}_stok`).text(newStok);
        product.new_qty = newQty;
        product.new_stok = newStok;

        let wholesale = product.product.wholesale;
        let price = product.product.price;
        let grosirPrice = product.grosir_price;

        let filterGrosirPrice = filterWholeSale(newQty, wholesale);
        let newGrosirPrice = filterGrosirPrice ? filterGrosirPrice.price / filterGrosirPrice.quantity : price;

        if (newGrosirPrice !== grosirPrice) {
            grosirPrice = newGrosirPrice;
            alert(filterGrosirPrice ? `Harga grosir ${filterGrosirPrice.price} per ${filterGrosirPrice.quantity} pcs` : 'Kembali ke harga awal per pcs');
        }

        let subtotal = newQty * grosirPrice;

        $(`#product_${product.product_id}_price`).val(formatRupiah(grosirPrice));
        $(`#product_${product.product_id}_subtotal`).val(formatRupiah(subtotal));
        product.grosir_price = grosirPrice;
        product.subtotal = subtotal;

        cart.set(`product_${product.product_id}`, product);
        $('#total-pay').text(formatRupiah(sumSubtotal(cart)));

        $('#clear, #simpan').prop('disabled', false);
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