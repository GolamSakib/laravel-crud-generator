@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Order</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('orders.store') }}">
                        @csrf

                        <div class="form-group row">
    <label for="order_number" class="col-md-4 col-form-label text-md-right">Order Number</label>
    <div class="col-md-6">
        <input id="order_number" type="text" class="form-control @error('order_number') is-invalid @enderror" name="order_number" value="{{ old('order_number', $order->order_number ?? '') }}" required>
        @error('order_number')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

                            <div class="form-group row">
        <label for="status" class="col-md-4 col-form-label text-md-right">Status</label>
        <div class="col-md-6">
            <select id="status" class="form-control @error('status') is-invalid @enderror" name="status" required>
                <option value="">Select Status</option>
                                            <option value="pending" {{ old('status', $order->status ?? '') == 'pending' ? 'selected' : '' }}>
                                {{ ucfirst('pending') }}
                            </option>
                            <option value="processing" {{ old('status', $order->status ?? '') == 'processing' ? 'selected' : '' }}>
                                {{ ucfirst('processing') }}
                            </option>
                            <option value="completed" {{ old('status', $order->status ?? '') == 'completed' ? 'selected' : '' }}>
                                {{ ucfirst('completed') }}
                            </option>
            </select>
            @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

                        <div class="form-group row">
    <label for="total" class="col-md-4 col-form-label text-md-right">Total</label>
    <div class="col-md-6">
        <input id="total" type="number" class="form-control @error('total') is-invalid @enderror" name="total" value="{{ old('total', $order->total ?? '') }}" required>
        @error('total')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

                        <div class="form-group row">
    <label for="notes" class="col-md-4 col-form-label text-md-right">Notes</label>
    <div class="col-md-6">
        <textarea id="notes" class="form-control @error('notes') is-invalid @enderror" name="notes" rows="3">{{ old('notes', $order->notes ?? '') }}</textarea>
        @error('notes')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Create Order
                                </button>
                                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                    Back to List
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection