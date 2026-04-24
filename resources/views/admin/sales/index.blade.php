@extends('layouts.app')

@section('content')
<div class="container mt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Sales</h3>
        <a href="{{ route('admin.pos.index') }}" class="btn btn-primary">Open POS</a>
    </div>

    <form method="GET" class="row g-2 mb-3 align-items-center">
        <div class="col-auto">
            <label class="form-label small mb-0">From</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control form-control-sm" />
        </div>
        <div class="col-auto">
            <label class="form-label small mb-0">To</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-control form-control-sm" />
        </div>
        <div class="col-auto">
            <label class="form-label small mb-0">&nbsp;</label>
            <div>
                <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                <a href="{{ route('admin.sales.index') }}" class="btn btn-sm btn-outline-secondary">Clear</a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
    <table class="table table-striped table-bordered align-middle" data-server-export="1" data-export-name="sales_list">
        <thead>
            <tr>
                <th>#</th>
                <th>Code</th>
                <th>Date</th>
                <th>Items</th>
                <th>Total</th>
                <th>Payments</th>
                <th>Change</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $s)
            <tr>
                <td>{{ $s->id }}</td>
                <td>{{ $s->code }}</td>
                <td>{{ $s->created_at->format('d-m-Y H:i') }}</td>
                <td>{{ $s->items->count() }}</td>
                <td>{{ 'Rp '.number_format($s->total ?? 0,0,',','.') }}</td>
                <td>{{ 'Rp '.number_format($s->payments->sum('amount') ?? 0,0,',','.') }}</td>
                <td>{{ 'Rp '.number_format(max(0, $s->payments->sum('amount') - ($s->total ?? 0)),0,',','.') }}</td>
                <td>{{ $s->status }}</td>
                <td>
                    <a href="{{ route('admin.sales.show', $s) }}" class="btn btn-sm btn-outline-primary">View</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>

    <div class="mt-3">{{ $sales->links('pagination::bootstrap-5') }}</div>
</div>
@endsection
