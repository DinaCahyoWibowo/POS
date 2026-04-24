@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Stock Movements</h3>
        <div>
            <a href="{{ route('admin.stock_movements.create') }}" class="btn btn-primary">Stock In</a>
        </div>
    </div>

    <form method="GET" class="row g-2 mb-3">
        <div class="col-auto">
            <input type="date" name="from" class="form-control" value="{{ request('from') }}" placeholder="From">
        </div>
        <div class="col-auto">
            <input type="date" name="to" class="form-control" value="{{ request('to') }}" placeholder="To">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
            <a href="{{ route('admin.stock_movements.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
    </form>

    <table class="table table-striped table-bordered align-middle" data-server-export="1" data-export-name="stock_movements">
        <thead>
            <tr>
                <th>#</th>
                <th>Type</th>
                <th>Note</th>
                <th>Item</th>
                <th class="text-end">Qty (base)</th>
                <th>Created By</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($movements as $m)
                @php $lines = $m->lines; @endphp
                @if($lines->isEmpty())
                    <tr>
                        <td>{{ $m->id }}</td>
                        <td>{{ ucfirst($m->type) }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($m->note, 50) }}</td>
                        <td>-</td>
                        <td class="text-end">0</td>
                        <td>{{ $m->created_by ? optional($m->createdBy)->name : '-' }}</td>
                        <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.stock_movements.show', $m) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @else
                    @foreach($lines as $line)
                        <tr>
                            <td>{{ $m->id }}</td>
                            <td>{{ ucfirst($m->type) }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($m->note, 50) }}</td>
                            <td>{{ $line->item_name ?? $line->item?->name }}</td>
                            <td class="text-end">
                                @php
                                    $qty = $line->qty;
                                    $baseUnitName = $line->item?->baseUnit?->name ?? $line->unit?->name ?? null;
                                    $unit = $baseUnitName ? ' (' . $baseUnitName . ')' : '';
                                    if($qty == (int)$qty) $qf = (int)$qty; else $qf = rtrim(rtrim(number_format($qty,4,'.',' '),'0'),'.');
                                    if($qty > 0) $qf = '+' . $qf;
                                @endphp
                                {{ $qf }}{!! $unit !!}
                            </td>
                            <td>{{ $m->created_by ? optional($m->createdBy)->name : '-' }}</td>
                            <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.stock_movements.show', $m) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

    {{ $movements->links('pagination::bootstrap-5') }}
</div>
@endsection
