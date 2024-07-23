<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use LaravelDaily\Invoices\Classes\Party;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Ubah kueri untuk hanya mengambil produk yang belum dihapus
            $data = Product::where('name', 'like', $request->search . '%')
                ->where('is_deleted', false)
                ->get();

            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                foreach ($data as $row) {
                    $output .= '<a href="#" onclick="pilihProduk(' . $row->id . ')"><li class="list-group-item" id="list-group-item">ID: ' . $row->id . ' | ' . $row->name . '</li></a>';
                }
                $output .= '</ul>';
            } else {
                $output .= '<li class="list-group-item">No Data Found</li>';
            }
            return $output;
        }
        return view('dashboard.transaction.index', [
            'title' => 'Transaction',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'subtotal' => 'required|numeric|min:0',
            'buyer_name' => 'required|string',
        ]);

        $user = Auth::user();
        $cart = $request->input('cart');
        $subtotal = $request->input('subtotal');
        $buyer = $request->input('buyer_name');

        $transaction = $user->transactions()->create([
            'buyer_name' => $buyer,
            'transaction_time' => now()->timezone('Asia/Jakarta'),
            'total_item' => count($cart),
            'total_price' => $subtotal
        ]);

        foreach ($cart as $item) {
            $transaction->details()->create([
                'transaction_id' => $transaction->id,
                'product_id' => $item['id'],
                'quantity' => $item['qty'],
                'price' => $item['grosir_price'],
                'subtotal' => $item['subtotal'],
            ]);

            $product = Product::findOrFail($item['id']);
            $product->update([
                'stok' => $product->stok - $item['qty'],
            ]);
        }
        return response()->json(['message' => 'Transaction success', 'data' => $transaction]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load('user', 'details.product');

        return view('dashboard.transaction.edit', [
            'title' => 'Edit Transaksi',
            'transaction' => $transaction,
            'print_method' => 'v2'
        ]);
    }

    public function getDetail(Transaction $transaction, Request $request)
    {
        if ($request->ajax()) {
            $transaction->load('user', 'details.product.wholesale');

            return response()->json($transaction);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Transaction $transaction, Request $request)
    {
        $request->validate([
            'cart' => 'required|array',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $cart = $request->input('cart');
        $subtotal = $request->input('subtotal');

        $transaction->update([
            'total_item' => count($cart),
            'total_price' => $subtotal,
        ]);

        // Update or create transaction details
        foreach ($cart as $item) {
            $transaction->details()->updateOrCreate(
                ['product_id' => $item['product_id']],
                [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['new_qty'],
                    'price' => $item['grosir_price'],
                    'subtotal' => $item['subtotal']
                ]
            );

            $product = Product::findOrFail($item['product_id']);
            $product->update([
                'stok' => $product->stok - ($item['new_qty'] - $item['quantity']),
            ]);
        }

        // Delete Transaction Details Not in Cart
        $deletedDetails = $transaction->details()
            ->whereNotIn('product_id', collect($cart)->pluck('product_id'))
            ->get();

        $transaction->details()
            ->whereNotIn('product_id', collect($cart)->pluck('product_id'))
            ->delete();

        // Increment product quantities by the deleted quantities
        foreach ($deletedDetails as $detail) {
            $product = Product::findOrFail($detail->product_id);
            $product->update([
                'stok' => $product->stok + $detail->quantity,
            ]);
        }


        return response()->json(['message' => 'Transaction updated successfully', 'data' => $transaction]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->update(['void' => 1]);

        return redirect('/dashboard/transactiondetails')->with('success', 'Transaction voided successfully.');
    }

    public function restore(Transaction $transaction)
    {
        $transaction->update(['void' => 0]);

        return redirect('/dashboard/transactiondetails/void')->with('success', 'Transaction restored successfully.');
    }

    public function success()
    {
        return redirect('/dashboard/transactiondetails')->with('success', 'Transaction has been created!');
    }

    public function showPrint(Transaction $transaction)
    {
        $invoice = $this->generateInvoice($transaction);
        return $invoice->stream();
    }

    public function getPrintUrl(Transaction $transaction)
    {
        $invoice = $this->generateInvoice($transaction);
        return response()->json(['url' => $invoice->url()], 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function downloadPrint(Transaction $transaction)
    {
        $invoice = $this->generateInvoice($transaction);
        return $invoice->download();
    }

    public function printAsHtml(Transaction $transaction)
    {
        $invoice = $this->generateInvoice($transaction);
        return $invoice->toHtml();
    }

    private function generateInvoice(Transaction $transaction)
    {
        $transaction->load('user', 'details.product');

        $client = new Party([
            'name' => $transaction->user->name,
            'address' => 'Jl. Hamara Effendi, Pataruman, Banjar',
            'custom_fields' => [
                'email' => $transaction->user->email,
                'paper_size' => '57mm'
            ]
        ]);

        $customer = new Party([
            'name' => $transaction->buyer_name,
        ]);

        $product = [];

        foreach ($transaction->details as $detail) {
            $product[] = InvoiceItem::make($detail->product->name)
                ->pricePerUnit($detail->price)
                ->quantity($detail->quantity);
        }

        $notes = [
            '-- Terima Kasih --',
        ];
        $notes = implode("<br>", $notes);

        $date = Carbon::parse($transaction->transaction_time);
        $filename = Str::slug($date->timestamp . '_' . $client->name . '_' . $customer->name);

        $invoice = Invoice::make('Samudra Kue')
            ->status(__('invoices::invoice.paid'))
            ->sequence($transaction->id)
            ->serialNumberFormat('{SERIES}-{SEQUENCE}')
            ->seller($client)
            ->buyer($customer)
            ->date($date)
            ->dateFormat('d-m-Y')
            ->currencySymbol('Rp')
            ->currencyCode('IDR')
            ->currencyFormat('{SYMBOL} {VALUE}')
            ->currencyThousandsSeparator('.')
            ->currencyDecimalPoint(',')
            ->filename($filename)
            ->addItems($product)
            ->notes($notes)
            ->save('public') // Save to /storage/app/public/
        ;

        return $invoice;
    }
}