<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\Product;
use App\Models\ProductRate;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(){
        try{
            $products = Product::all();
            return $products;
        }catch(Throwable $th){
            return response()->json('some thing went wrong', 403);
        }
    }

    public function show($id)
{
    try {
        $product = Product::findOrFail($id);
        $productRate = ProductRate::where('product_id', $id)->get();

        $totalRate = 0;
        $totalRaters = count($productRate); // Count the number of raters

        foreach($productRate as $rate) {
            $totalRate += $rate->rate; // Accumulate the total rate
        }

        // Calculate the average rate if there are raters
        $productTotalRate = ($totalRaters > 0) ? ($totalRate / $totalRaters) : 0;

        return response()->json(compact('product', 'productTotalRate'));
    } catch (ModelNotFoundException $exception) {
        return response()->json(['error' => 'Product not found'], 404);
    } catch (\Throwable $th) {
        return response()->json(['error' => 'Something went wrong'], 500);
    }
}


    public function store(Request $request)
    {
        try {
            $imagePath = $request->file('image')->store('products', 'public');
            $product = new Product;
            $product->name = $request->name;
            $product->image = $imagePath; 
            $product->price = $request->price;
            $product->save();

            return response()->json(['success' => 'product added successfully'], 200);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $product = Product::findorfail($id);
            $product->delete();
            return response()->json(['success' => 'deleted successfully'], 200);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

}
