<?php

namespace App\Http\Controllers;

use App\Models\ProductRate;
use Illuminate\Http\Request;

class rateController extends Controller
{
    /*
    {
        "id": 1,
        "user_id": 2,
        "rate": 1-5,
    }
    */
    public function store(Request $request)
    {
        try {

            $rate = new ProductRate;
            $rate->product_id = $request->id;
            $rate->user_id = $request->user_id;
            $rate->rate = $request->rate;
            $rate->save();

            return response()->json(['success' => 'rate added successfully'], 200);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
