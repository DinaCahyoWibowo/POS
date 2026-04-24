@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Stock Report</h3>

    </div>

    <form class="row g-2 mb-3" method="GET">
        <div class="col-auto">
            <input type="date" name="from" class="form-control" value="{{ optional($start)->format('Y-m-d') }}">
        </div>
        <div class="col-auto">
            <input type="date" name="to" class="form-control" value="{{ optional($end)->format('Y-m-d') }}">
        </div>
        <div class="col-auto">
            <button class="btn btn-primary">Filter</button>
        </div>
    </form>

        <div class="card">
            <div class="card-body">
                @php $rows = $rows ?? collect(); @endphp
                <style>
                    .stock-report-table thead th { text-align: center; vertical-align: middle; }
                    .stock-report-table tbody td { text-align: left; vertical-align: middle; white-space: normal; overflow: visible; }
                    .stock-report-table tfoot th, .stock-report-table tfoot td { font-weight: 600; }
                </style>
                <script>
                    (function(){
                        function fitStockTables(){
                            document.querySelectorAll('.stock-report-table').forEach(function(table){
                                table.style.fontSize = '';
                                var tbody = table.tBodies[0]; if (!tbody) return;
                                var tds = Array.from(tbody.querySelectorAll('td')); if (tds.length === 0) return;
                                function anyWrapped(){ return tds.some(function(cell){ return (cell.scrollHeight - cell.clientHeight) > 1; }); }
                                if (!anyWrapped()) return;
                                var fs = parseFloat(window.getComputedStyle(table).fontSize) || 14; var minFs = 10;
                                while (anyWrapped() && fs > minFs){ fs = Math.max(minFs, fs - 1); table.style.fontSize = fs + 'px'; }
                            });
                        }
                        document.addEventListener('DOMContentLoaded', function(){ fitStockTables(); });
                        var _srTimer = null; window.addEventListener('resize', function(){ clearTimeout(_srTimer); _srTimer = setTimeout(fitStockTables, 120); });
                    })();
                </script>

                <table class="table table-bordered table-striped stock-report-table" data-include-totals="1" data-server-export="1" data-export-name="stock_report">
                    <thead>
                        <tr>
                            <th class="sortable" data-type="string"><a href="#" class="js-sort">Item <i class="bi"></i></a></th>
                            <th class="sortable" data-type="string"><a href="#" class="js-sort">Category <i class="bi"></i></a></th>
                            <th class="text-end">Stock In</th>
                            <th class="text-end">Stock Out</th>
                            <th class="text-end">Remaining</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $r)
                        <tr>
                            <td>{{ $r->item_name }}</td>
                            <td>{{ $r->category_name }}</td>
                            <td class="text-end">{{ number_format($r->stock_in ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($r->stock_out ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($r->remaining ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center">No items found for the selected period</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-end">Total :</th>
                            <th></th>
                            <th class="text-end">{{ number_format($totalStockIn ?? 0, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($totalStockOut ?? 0, 0, ',', '.') }}</th>
                            <th class="text-end">{{ number_format($totalRemaining ?? 0, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
                <div>
                    {{ $rows->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
</div>
@endsection
