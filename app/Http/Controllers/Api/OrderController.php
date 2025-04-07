<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return response()->json($orders);
    }

    public function store(OrderRequest $request)
    {
        $orders = Order::create($request->validated());
        return response()->json($orders, Response::HTTP_CREATED);
    }

    public function show(Order $orders)
    {
        return response()->json($orders);
    }

    public function update(OrderRequest $request, Order $orders)
    {
        $orders->update($request->validated());
        return response()->json($orders);
    }

    public function destroy(Order $orders)
    {
        $orders->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}