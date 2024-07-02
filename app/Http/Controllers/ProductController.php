<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wholesale;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.product.index', [
            'title' => 'Product',
            'products' => Product::latest()->filter()->paginate(9)->withQueryString(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.product.create', [
            'title' => 'Buat Produk',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products',
            'price' => 'required|numeric',
            'stok' => 'required|numeric',
            'wholesale_prices.*' => 'nullable|numeric', // Tambahkan nullable karena ini bisa kosong
            'quantities.*' => 'nullable|integer', // Tambahkan nullable karena ini bisa kosong
        ]);

        // Buat produk baru
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'stok' => $request->stok,
        ]);

        // Simpan harga grosir jika diisi
        if ($request->has('wholesale_prices')) {
            foreach ($request->wholesale_prices as $key => $price) {
                if (!empty($price) && !empty($request->quantities[$key])) {
                    Wholesale::create([
                        'product_id' => $product->id,
                        'price' => $price,
                        'quantity' => $request->quantities[$key],
                    ]);
                }
            }
        }

        return redirect('/dashboard/product')->with('success', 'Product has been created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        if ($request->ajax()) {
            $product = Product::with('wholesale')->find($request->id);

            if (!$product) {
                return response()->json(['message' => 'Product not found'], 404);
            }

            return response()->json($product);
        }

        return response()->json(['message' => 'Resource not found'], 404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        return view('dashboard.product.edit', [
            'title' => 'Edit Produk',
            'product' => $product,
            'wholesales' => Wholesale::where('product_id', $product->id)->get(), // Ambil semua harga grosir terkait
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric|min:1',
            'stok' => 'required|numeric|min:0',
            'wholesale_prices.*' => 'required|numeric|min:1', // Validasi harga grosir
            'quantities.*' => 'required|numeric|min:1', // Validasi jumlah minimum pembelian
        ]);

        // Simpan perubahan pada produk
        $product->update([
            'name' => $request->name,
            'price' => $request->price,
            'stok' => $request->stok,
        ]);

        $allWholesale = Wholesale::where('product_id', $product->id)->get()->toArray();
        // Dapatkan daftar semua id dari $allWholesale
        $allWholesaleIds = collect($allWholesale)->pluck('id')->toArray();

        if ($allWholesaleIds != null) {
            if ($request->wholesale_prices == null) {
                Wholesale::where('product_id', $product->id)->delete();
            } else {
                // Dapatkan daftar id yang perlu dihapus
                $idsToDelete = array_diff($allWholesaleIds, array_keys($request->wholesale_prices));
                // Hapus entri dari database yang memiliki id tersebut
                Wholesale::whereIn('id', $idsToDelete)->delete();
            }
        }

        // Simpan perubahan atau tambah data grosir
        if ($request->wholesale_prices != null) {
            foreach ($request->wholesale_prices as $key => $price) {
                $wholesale = Wholesale::where('id', $key)
                    ->where('product_id', $product->id)
                    ->first();
                if ($wholesale) {
                    if ($wholesale->price != $price || $wholesale->quantity != $request->quantities[$key]) {
                        $wholesale->update([
                            'price' => $price,
                            'quantity' => $request->quantities[$key],
                        ]);
                    }
                } else {
                    // Membuat wholesale baru
                    $newWholesale = Wholesale::create([
                        'product_id' => $product->id,
                        'price' => $price,
                        'quantity' => $request->quantities[$key],
                    ]);

                    if (!$newWholesale) {
                        return redirect()->back()->with('error', 'Failed to create new wholesale.');
                    }
                }
            }
        }

        return redirect('/dashboard/product')->with('success', 'Product has been updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        Product::destroy($product->id);
        Wholesale::where('product_id', $product->id)->delete();
        return redirect('/dashboard/product')->with('success', 'Product has been Deleted!');
    }
}
