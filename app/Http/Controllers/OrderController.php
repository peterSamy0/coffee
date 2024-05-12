<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Order_item;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // show all orders api: http://localhost:8000/api/orders  (method: get)
    public function index()
    {
        try{
            $orders = Order::with([
                'user:id,name',
                'order_items.product:id,name,price'
            ])->get();
            return $orders;
        }catch(Throwable $th){
            return response()->json('error: some thing went wrong', 403);
        }
    }
    // to show spicific order api: http://localhost:8000/api/orders/id  (method: get)
    public function show($id)
    {
        try {
            $order = Order::with([
                'user:id,name',
                'order_items.product:id,name,price'
            ])->findOrFail($id);
    
            return $order;
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    // to make order for example api: http://localhost:8000/api/orders (method: post)
    /*
    {
        "user_id": 2,
        "products": [1,2]
    }
    */
    public function store(Request $request)
    {
        try {
            $order = new Order;
            $order->user_id = $request->user_id;

            $totalPrice = 0;
            $products = $request->products;
            foreach ($products as $product) {
                $productData = Product::findorfail($product);
                $totalPrice += $productData->price;
            }
            $order->total_price = $totalPrice;
            $order->save();
    
            $products = $request->products;
            foreach ($products as $product) {
                $order_item = new Order_item;
                $order_item->product_id = $product;
                $order_item->order_id = $order->id;
                $order_item->save();
            }
            return response()->json(['success' => 'Order added successfully'], 200);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
    // to delete spicific order api: http://localhost:8000/api/orders/id (method: delete)
    public function destroy($id)
    {
        try {
            $order = Order::findorfail($id);
            $order->delete();
            return response()->json(['success' => 'deleted successfully'], 200);
        }
        catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
