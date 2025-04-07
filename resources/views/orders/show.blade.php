@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Order Details</div>

                <div class="card-body">
                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Order Number:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $order->order_number }}</p>
    </div>
</div>

                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Status:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $order->status }}</p>
    </div>
</div>

                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Total:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $order->total }}</p>
    </div>
</div>

                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Notes:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $order->notes }}</p>
    </div>
</div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-primary">
                                Edit
                            </a>
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                Back to List
                            </a>
                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection