@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Units</h3>
        @if(auth()->user()?->role === 'admin')
            <a href="{{ route('admin.units.create') }}" class="btn btn-primary">Create Unit</a>
        @endif
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <table class="table table-striped table-bordered align-middle" data-server-export="1" data-export-name="units_list">
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
            @foreach($units as $u)
            <tr>
                <td>{{ $u->id }}</td>
                <td>{{ $u->name }}</td>
                <td>{{ $u->code }}</td>
                <td>{{ $u->description }}</td>
                <td>
                    @if(auth()->user()?->role === 'admin')
                        <a href="{{ route('admin.units.edit', $u) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        <form action="{{ route('admin.units.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this unit? Items using this unit will be affected. Continue?');">
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

    {{ $units->links('pagination::bootstrap-5') }}
</div>
@endsection
