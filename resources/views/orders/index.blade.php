@extends('layouts.app')

@section('content')
<div class="container">
    <h1>orders List</h1>
    <a href="{{ route('orders.create') }}" class="btn btn-primary">Create New Order</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Order Number</th>
                <th>Status</th>
                <th>Total</th>
                <th>Notes</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->order_number }}</td>
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->total }}</td>
                    <td>{{ $order->notes }}</td>
                    <td>
                        <a href="{{ route('orders.edit', $order) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection