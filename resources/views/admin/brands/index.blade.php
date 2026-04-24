@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Brands</h3>
        @if(auth()->user()?->role === 'admin')
            <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">Create Brand</a>
        @endif
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <table class="table table-striped table-bordered align-middle" data-server-export="1" data-export-name="brands_list">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Code</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($brands as $b)
            <tr>
                <td>{{ $b->id }}</td>
                <td>{{ $b->name }}</td>
                <td>{{ $b->code }}</td>
                <td>{{ $b->description }}</td>
                <td>
                    @if(auth()->user()?->role === 'admin')
                        <a href="{{ route('admin.brands.edit', $b) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.brands.destroy', $b) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this brand? Items using this brand will be affected. Continue?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $brands->links('pagination::bootstrap-5') }}
</div>
@endsection
