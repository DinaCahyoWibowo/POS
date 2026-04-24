@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Users</h3>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">Add User</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="GET" class="mb-3">
        <div class="row g-2">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search by name, username or email">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">All roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrators</option>
                    <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>Users</option>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-primary">Filter</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-link">Reset</a>
            </div>
        </div>
    </form>

    <!-- styles moved to layout -->

    <form method="POST" id="bulk-delete-form" action="{{ route('admin.users.bulkDelete') }}">
        @csrf
        <table class="table table-striped table-bordered table-hover align-middle" data-server-export="1" data-export-name="users_list">
        <thead>
            <tr>
                <th style="width:1%"><input type="checkbox" id="select-all"></th>
                
                <th class="sortable" data-sort="id" data-type="number">
                    @php $dir = request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc'; @endphp
                    <a class="js-sort d-inline-flex align-items-center gap-1" href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => $dir])) }}"># <i class="bi" aria-hidden="true"></i></a>
                </th>
                <th class="sortable" data-sort="name" data-type="string">
                    @php $dir = request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'; @endphp
                    <a class="js-sort d-inline-flex align-items-center gap-1" href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => $dir])) }}">Name <i class="bi" aria-hidden="true"></i></a>
                </th>
                <th class="sortable" data-sort="username" data-type="string">
                    @php $dir = request('sort') === 'username' && request('direction') === 'asc' ? 'desc' : 'asc'; @endphp
                    <a class="js-sort d-inline-flex align-items-center gap-1" href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'username', 'direction' => $dir])) }}">Username <i class="bi" aria-hidden="true"></i></a>
                </th>
                <th class="sortable" data-sort="email" data-type="string">
                    @php $dir = request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'; @endphp
                    <a class="js-sort d-inline-flex align-items-center gap-1" href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => $dir])) }}">Email <i class="bi" aria-hidden="true"></i></a>
                </th>
                <th class="sortable" data-sort="role" data-type="string">
                    @php $dir = request('sort') === 'role' && request('direction') === 'asc' ? 'desc' : 'asc'; @endphp
                    <a class="js-sort d-inline-flex align-items-center gap-1" href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'role', 'direction' => $dir])) }}">Role <i class="bi" aria-hidden="true"></i></a>
                </th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td><input type="checkbox" name="ids[]" value="{{ $user->id }}"></td>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->username }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role_name ?? $user->role }}</td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
        <div class="d-flex justify-content-between">
            <div>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete selected users?')">Delete Selected</button>
            </div>
            <div>
                {{ $users->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </form>

    <script>
        document.getElementById('select-all').addEventListener('change', function(e){
            const checked = e.target.checked;
            document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = checked);
        });
    </script>

    <!-- sorting moved to layout for site-wide application -->

</div>
@endsection
