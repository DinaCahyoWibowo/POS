@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Stock Opname - {{ $opname->code }}</h3>
        <div>{{ \Carbon\Carbon::parse($opname->opname_date)->format('d-m-Y') }}</div>
    </div>

    <div class="card">
        <div class="card-body">
            <p><strong>Alasan:</strong> {{ $opname->reason }}</p>
            <table class="table table-bordered">
                <thead><tr><th>Item</th><th class="text-end">Stok Sistem</th><th class="text-end">Stok Fisik</th><th class="text-end">Selisih</th><th>Alasan</th></tr></thead>
                <tbody>
                    @foreach($opname->lines as $line)
                    <tr>
                        <td>{{ $line->item?->name }}</td>
                        <td class="text-end">{{ number_format(round($line->system_qty), 0, '.', '') }}</td>
                        <td class="text-end">{{ number_format(round($line->physical_qty), 0, '.', '') }}</td>
                        <td class="text-end">{{ number_format(round($line->physical_qty - $line->system_qty), 0, '.', '') }}</td>
                        <td>{{ $line->reason ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
