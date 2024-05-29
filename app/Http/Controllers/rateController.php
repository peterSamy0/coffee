<?php

namespace App\Http\Controllers;

use App\Models\ProductRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class rateController extends Controller
{

    function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function store(Request $request)
    {
        try {
            $user = Auth::user(); // Get the authenticated user

            $rate = new ProductRate;
            $rate->product_id = $request->id;
            $rate->user_id = $user->id; // Assign the authenticated user's ID
            $rate->rate = $request->rate;
            $rate->save();

            return response()->json(['success' => 'Rate added successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}