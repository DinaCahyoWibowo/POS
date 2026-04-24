@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Stock Opname</h3>
        <a href="{{ route('admin.stock_opnames.create') }}" class="btn btn-primary">Tambah Opname</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr><th>Tanggal</th><th>Code</th><th>Lines</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($opnames as $o)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($o->opname_date)->format('d-m-Y') }}</td>
                        <td>{{ $o->code }}</td>
                        <td>{{ $o->lines_count ?? $o->lines()->count() }}</td>
                        <td><a href="{{ route('admin.stock_opnames.show', $o) }}" class="btn btn-sm btn-outline-secondary">Detail</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">Tidak ada opname</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div>{{ $opnames->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
