@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Stock Movement #{{ $movement->id }}</h3>
        <a href="{{ route('admin.stock_movements.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card p-3">
        <div class="mb-2"><strong>Type:</strong> {{ ucfirst($movement->type) }}</div>
        <div class="mb-2"><strong>Note:</strong> {{ $movement->note }}</div>
        <div class="mb-2"><strong>Date:</strong> {{ $movement->created_at->format('Y-m-d H:i') }}</div>

        <table class="table table-sm mt-3">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Unit</th>
                    <th class="text-end">Factor</th>
                    <th class="text-end">Qty (base)</th>
                    <th class="text-end">Cost</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movement->lines as $line)
                <tr>
                    <td>{{ $line->item_name ?? $line->item?->name }}</td>
                    <td>{{ $line->unit?->name }}</td>
                    <td class="text-end">{{ $line->factor }}</td>
                    <td class="text-end">{{ $line->qty }}</td>
                    <td class="text-end">{{ $line->cost_price ? 'Rp '.number_format($line->cost_price,0,',','.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
