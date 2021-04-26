<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\CheckoutRequest;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function checkout(CheckoutRequest $request)
    {
        //ambil data kecuali transaction_details
        $data = $request->except('transaction_details');
        $data['uuid'] = 'TRX' . mt_rand(10000, 99999) . mt_rand(100, 999);

        $transaction = Transaction::create($data);

        //ambil array data kiriman user
        foreach ($request->transaction_details as $id_product) 
        {
            $details[] = new TransactionDetail([
                'transactions_id' => $transaction->id,
                'products_id' => $id_product,
            ]);

            //kurangi product
            Product::find($id_product)->decrement('quantity');
        }

        //simpan semua transaction_detail
        $transaction->details()->saveMany($details);

        return ResponseFormatter::success($transaction);
    }
}
