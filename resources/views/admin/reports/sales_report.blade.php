@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2>Sales Report</h2>
    <style>
        /* Table header centered; table body cells left-aligned. We allow wrapping in CSS
           and rely on JS to downsize font uniformly so each cell fits on a single line. */
        .sales-report-table thead th { text-align: center; vertical-align: middle; }
        .sales-report-table tbody td { text-align: left; vertical-align: middle; white-space: normal; overflow: visible; }
        /* Keep the sale date with a fixed width to avoid excessive wrapping */
        .sales-report-table th.col-date, .sales-report-table td.col-date { width: 180px; }
        /* Footer totals bold */
        .sales-report-table tfoot th, .sales-report-table tfoot td { font-weight: 600; }
    </style>

    <script>
        (function(){
            // Reduce table font-size uniformly until every tbody cell fits on a single line
            function fitSalesReportTables(){
                document.querySelectorAll('.sales-report-table').forEach(function(table){
                    // Reset font-size so we can compute natural sizes
                    table.style.fontSize = '';
                    var tbody = table.tBodies[0];
                    if (!tbody) return;
                    var tds = Array.from(tbody.querySelectorAll('td'));
                    if (tds.length === 0) return;

                    // compute single-line height using a sample cell
                    var sample = tds.find(function(e){ return e.offsetHeight > 0; }) || tds[0];
                    var lineHeight = parseFloat(window.getComputedStyle(sample).lineHeight) || (parseFloat(window.getComputedStyle(sample).fontSize) * 1.2);

                    // helper: does any cell wrap (i.e. scrollHeight > clientHeight by a small epsilon)
                    function anyWrapped(){
                        return tds.some(function(cell){
                            // allow small rounding differences
                            return (cell.scrollHeight - cell.clientHeight) > 1;
                        });
                    }

                    // If nothing wrapped, leave as-is
                    if (!anyWrapped()) return;

                    // Gradually reduce font-size applied to the table until cells fit or min reached
                    var computed = window.getComputedStyle(table).fontSize;
                    var fs = parseFloat(computed) || 14;
                    var minFs = 10; // do not reduce beyond this
                    while (anyWrapped() && fs > minFs){
                        fs = Math.max(minFs, fs - 1);
                        table.style.fontSize = fs + 'px';
                        // allow browser to reflow
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function(){ fitSalesReportTables(); });
            var _srTimer = null;
            window.addEventListener('resize', function(){ clearTimeout(_srTimer); _srTimer = setTimeout(fitSalesReportTables, 120); });
        })();
    </script>

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

    <table class="table table-bordered table-striped sales-report-table" data-include-totals="1" data-server-export="1" data-export-name="sales_report">
        <thead>
            <tr>
                <th class="sortable" data-type="string"><a href="#" class="js-sort">Sale Code <i class="bi"></i></a></th>
                <th class="sortable col-date" data-type="string"><a href="#" class="js-sort">Sale Date <i class="bi"></i></a></th>
                <th>Item Name</th>
                <th>Item Qty (unit)</th>
                <th class="numeric">Cost at Sale</th>
                <th class="numeric">Price at Sale</th>
                <th class="numeric">Profit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $r)
                @php
                    $unitFactor = (float) ($r->unit_factor ?? 1);
                    $baseQty = (float)$r->qty * $unitFactor;
                    $costAtSale = (float) $r->cost_at_sale;
                    $priceAtSale = (float) $r->price_at_sale;
                    $profitNow = $priceAtSale - $costAtSale; // price minus cost
                @endphp
                <tr>
                    <td>{{ $r->sale_code }}</td>
                    <td class="col-date">{{ \Carbon\Carbon::parse($r->sale_date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i') }}</td>
                    <td>{{ $r->item_name ?? $r->name ?? $r->item_code ?? '-' }}</td>
                    <td>{{ (int)$r->qty }} {{ $r->unit_code ?? $r->unit_name }} @if($r->base_unit_name) ({{ (int)$baseQty }} {{ $r->base_unit_name }}) @endif</td>
                    <td class="numeric">{{ 'Rp '.number_format($costAtSale ?? 0,0,',','.') }}</td>
                    <td class="numeric">{{ 'Rp '.number_format($priceAtSale ?? 0,0,',','.') }}</td>
                    <td class="numeric">{{ 'Rp '.number_format($profitNow ?? 0,0,',','.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="text-end">Total :</th>
                <th class="numeric">{{ 'Rp '.number_format($totalCostAtSale ?? 0,0,',','.') }}</th>
                <th class="numeric">{{ 'Rp '.number_format($totalPriceAtSale ?? 0,0,',','.') }}</th>
                <th class="numeric">{{ 'Rp '.number_format($totalProfitNow ?? 0,0,',','.') }}</th>
            </tr>
        </tfoot>
    </table>

    <div>
        {{ $rows->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
