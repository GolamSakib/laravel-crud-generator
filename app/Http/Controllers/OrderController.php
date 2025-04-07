<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\OrderRequest;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        
        return view('orders.create');
    }

    public function store(OrderRequest $request)
    {
        $orders = Order::create($request->validated());
        return redirect()->route('orders.index')
            ->with('success', 'Order created successfully.');
    }

    public function show(Order $orders)
    {
        return view('orders.show', compact('orders'));
    }

    public function edit(Order $orders)
    {
        return view('orders.edit', compact('orders'));
    }

    public function update(OrderRequest $request, Order $orders)
    {
        $orders->update($request->validated());
        return redirect()->route('orders.index')
            ->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $orders)
    {
        $orders->delete();
        return redirect()->route('orders.index')
            ->with('success', 'Order deleted successfully.');
    }
}