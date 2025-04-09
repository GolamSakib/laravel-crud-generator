@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Project Details</div>

                <div class="card-body">
                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Name:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $project->name }}</p>
    </div>
</div>

                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Description:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $project->description }}</p>
    </div>
</div>

                    <div class="form-group row">
    <label class="col-md-4 text-md-right">Status:</label>
    <div class="col-md-6">
        <p class="form-control-static">{{ $project->status }}</p>
    </div>
</div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <a href="{{ route('projects.edit', $project->id) }}" class="btn btn-primary">
                                Edit
                            </a>
                            <a href="{{ route('projects.index') }}" class="btn btn-secondary">
                                Back to List
                            </a>
                            <form action="{{ route('projects.destroy', $project->id) }}" method="POST" class="d-inline">
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