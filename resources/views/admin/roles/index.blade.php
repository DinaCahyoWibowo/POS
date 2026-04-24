@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Roles</h3>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Create Role</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-striped table-bordered table-hover align-middle" data-server-export="1" data-export-name="roles_list">
        <thead>
            <tr>
                <th class="sortable" data-sort="id" data-type="number"># <span class="ms-1 sort-icon"><i class="bi bi-arrow-down-up"></i></span></th>
                <th class="sortable" data-sort="name" data-type="string">Name <span class="ms-1 sort-icon"><i class="bi bi-arrow-down-up"></i></span></th>
                <th class="sortable" data-sort="slug" data-type="string">Slug <span class="ms-1 sort-icon"><i class="bi bi-arrow-down-up"></i></span></th>
                <th class="sortable" data-sort="description" data-type="string">Description <span class="ms-1 sort-icon"><i class="bi bi-arrow-down-up"></i></span></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles as $role)
                <tr>
                    <td>{{ $role->id }}</td>
                    <td>{{ $role->name }}</td>
                    <td>{{ $role->slug }}</td>
                    <td>{{ $role->description }}</td>
                    <td>
                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-secondary">Edit</a>
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete this role?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <style>
        .table thead th.sortable { cursor: pointer; }
        .sort-icon i { font-size: 0.9rem; vertical-align: middle; }
        th.sortable.asc .sort-icon i { transform: rotate(0); }
        th.sortable.desc .sort-icon i { transform: rotate(180deg); }
    </style>

    <script>
        // lightweight client-side sorting for roles table (current page only)
        (function(){
            const table = document.querySelector('.table');
            if (!table) return;
            const tbody = table.querySelector('tbody');
            const headers = document.querySelectorAll('th.sortable');

            function getCellValue(row, idx){
                const cells = row.children;
                if (!cells[idx]) return '';
                return cells[idx].innerText.trim();
            }

            function comparer(idx, type, asc){
                return function(a,b){
                    let v1 = getCellValue(asc ? a : b, idx);
                    let v2 = getCellValue(asc ? b : a, idx);
                    if (type === 'number'){
                        v1 = parseInt(v1) || 0; v2 = parseInt(v2) || 0;
                        return v1 - v2;
                    }
                    return v1.localeCompare(v2, undefined, {numeric: true, sensitivity: 'base'});
                };
            }

            headers.forEach((th)=>{
                th.addEventListener('click', function(e){
                    if (e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                    e.preventDefault();
                    const colIndex = Array.from(th.parentNode.children).indexOf(th);
                    const type = th.dataset.type || 'string';
                    const currentlyAsc = th.classList.contains('asc');
                    const asc = !currentlyAsc;
                    // reset classes and icons
                    document.querySelectorAll('th.sortable').forEach(h=>{
                        h.classList.remove('asc','desc');
                        const ic = h.querySelector('.sort-icon i');
                        if (ic) ic.className = 'bi bi-arrow-down-up';
                    });
                    th.classList.add(asc ? 'asc' : 'desc');
                    const icon = th.querySelector('.sort-icon i');
                    if (icon) {
                        icon.className = asc ? 'bi bi-sort-up' : 'bi bi-sort-down';
                    }
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    rows.sort(comparer(colIndex, type, asc));
                    rows.forEach(r => tbody.appendChild(r));
                });
            });
        })();
    </script>

    {{ $roles->links('pagination::bootstrap-5') }}
</div>
@endsection
