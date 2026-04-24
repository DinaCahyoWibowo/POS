@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Edit Role - {{ $role->name }}</h3>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.roles.update', $role) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Slug</label>
            <input type="text" name="slug" value="{{ old('slug', $role->slug) }}" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control">{{ old('description', $role->description) }}</textarea>
        </div>
        <button class="btn btn-primary">Save</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div>
@endsection
