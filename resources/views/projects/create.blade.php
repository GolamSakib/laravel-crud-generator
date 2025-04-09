@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Create Project</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('projects.store') }}">
                        @csrf

                        <div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">Name</label>
    <div class="col-md-6">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $project->name ?? '') }}" required>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>
</div>

                        <div class="form-group row">
    <label for="description" class="col-md-4 col-form-label text-md-right">Description</label>
    <div class="col-md-6">
        <textarea id="description" class="form-control @error('description') is-invalid @enderror" name="description" rows="3">{{ old('description', $project->description ?? '') }}</textarea>
        @error('description')
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
                                            <option value="open" {{ old('status', $project->status ?? '') == 'open' ? 'selected' : '' }}>
                                {{ ucfirst('open') }}
                            </option>
                            <option value="closed" {{ old('status', $project->status ?? '') == 'closed' ? 'selected' : '' }}>
                                {{ ucfirst('closed') }}
                            </option>
            </select>
            @error('status')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
    </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Create Project
                                </button>
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary">
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