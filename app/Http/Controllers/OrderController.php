<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Order_item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    public function index()
    {
        try {
            $user = Auth::user();


            $orders = Order::with([
                'user:id,name',
                'order_items.product:id,name,price'
            ])->where('user_id', $user->id)->get();

            return $orders;
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    // public function show($id)
    // {
    //     try {
    //         $user = Auth::user();


    //         $order = Order::with([
    //             'user:id,name',
    //             'order_items.product:id,name,price'
    //         ])->where('user_id', $user->id)->findOrFail($id);

    //         return $order;
    //     } catch (ModelNotFoundException $exception) {
    //         return response()->json(['error' => 'Order not found'], 404);
    //     } catch (\Throwable $th) {
    //         return response()->json(['error' => 'Something went wrong'], 500);
    //     }
    // }



    public function show(Request $request)
    {
        try {
            $user = Auth::user();

            // Retrieve the order ID from the query parameters
            $orderId = $request->query('order_id');

            // Check if the order ID query parameter is provided
            if (!$orderId) {
                return response()->json(['error' => 'Order ID query parameter is required'], 400);
            }

            $order = Order::with([
                'user:id,name',
                'order_items.product:id,name,price'
            ])->where('user_id', $user->id)->findOrFail($orderId);

            return $order;
        } catch (ModelNotFoundException $exception) {
            return response()->json(['error' => 'Order not found'], 404);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $user = Auth::user();


            $order = new Order;
            $order->user_id = $user->id;


            $totalPrice = 0;
            foreach ($request->products as $productId) {
                $product = Product::findOrFail($productId);
                $totalPrice += $product->price;
            }


            DB::beginTransaction();


            $order->total_price = $totalPrice;
            $order->save();


            foreach ($request->products as $productId) {
                $orderItem = new Order_item;
                $orderItem->product_id = $productId;
                $orderItem->order_id = $order->id;
                $orderItem->save();
            }


            DB::commit();

            return response()->json(['success' => 'Order added successfully'], 200);
        } catch (\Throwable $th) {

            DB::rollBack();

            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $user = Auth::user();


            if ($user->usertype !== 'admin') {
                return response()->json(['error' => 'Unauthorized'], 403);
            }


            $order = Order::findorfail($id);
            $order->delete();

            return response()->json(['success' => 'Deleted successfully'], 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}