<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\TransactionDetails;
use App\Http\Requests\StoreTransactionDetailsRequest;
use App\Http\Requests\UpdateTransactionDetailsRequest;
Use Illuminate\Http\Request;

class TransactionDetailsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::where('name', 'like', $request->search . '%')->get();
            $output = '';
            if (count($data) > 0) {
                $output = '<ul class="list-group" style="display: block; position: relative; z-index: 1">';
                foreach ($data as $row) {
                    $output .= '<li class="list-group-item" id="list-group-item">'.$row->name.'</li>';
                }
                $output .= '</ul>';
            }
            else {
                $output .= '<li class="list-group-item">No Data Found</li>';
            }
            return $output;
        }
        return view('dashboard.transactiondetails.index', [
            'title' => 'Transactions',
            'transaction' => TransactionDetails::all(),
            'product' =>  Product::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTransactionDetailsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionDetails $transactionDetails)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionDetails $transactionDetails)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTransactionDetailsRequest $request, TransactionDetails $transactionDetails)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionDetails $transactionDetails)
    {
        //
    }
}
