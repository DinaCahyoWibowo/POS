<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function __construct()
    {
        // stock report accessible to inventory and admin; sales report accessible to sales and admin
        $this->middleware('role:admin,inventory')->only(['stockReport']);
        $this->middleware('role:admin,sales')->only(['salesReport']);
        $this->middleware('admin')->except(['stockReport','salesReport']);
    }
    public function salesReport(Request $request)
    {
        $tz = config('app.timezone');
        $from = $request->input('from');
        $to = $request->input('to');

        // default to current month
        $start = $from ? \Carbon\Carbon::parse($from, $tz)->startOfDay() : \Carbon\Carbon::now($tz)->startOfMonth();
        $end = $to ? \Carbon\Carbon::parse($to, $tz)->endOfDay() : \Carbon\Carbon::now($tz)->endOfMonth();

        $query = DB::table('sale_items as si')
            ->join('sales as s','si.sale_id','s.id')
            ->join('items as it','si.item_id','it.id')
            ->leftJoin('units as u','si.unit_id','u.id')
            ->leftJoin('units as bu','it.base_unit_id','bu.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id','=','si.item_id')->on('iu.unit_id','=','si.unit_id');
            })
            ->whereBetween('s.created_at', [$start, $end])
            ->selectRaw(implode(',', [
                's.id as sale_id',
                's.code as sale_code',
                's.created_at as sale_date',
                'it.name as item_name',
                'si.id as sale_item_id',
                'si.qty as qty',
                "COALESCE(si.unit_factor, iu.factor, 1) as unit_factor",
                'u.name as unit_name',
                'u.code as unit_code',
                'bu.name as base_unit_name',
                // cost at sale (prefer stored cost_total, fallback to computed)
                'COALESCE(si.cost_total, (si.cost_price * si.qty * COALESCE(si.unit_factor, iu.factor,1))) as cost_at_sale',
                // price at sale
                'si.line_total as price_at_sale',
                // current cost per base & current price per sold unit (prefer item_units.price if present)
                'it.cost_price as current_cost_per_base',
                'COALESCE(iu.price, it.sell_price * COALESCE(si.unit_factor, iu.factor,1)) as current_price_per_unit'
            ]))
            ->orderBy('s.created_at','desc');

        $rows = $query->paginate(50)->withQueryString();

        

        // If export requested, return full filtered dataset (no pagination)
        if ($request->has('export')) {
            $fmt = $request->input('export'); // 'csv','xls' or 'pdf'
            $all = $query->get();
            $filename = 'sales_report_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');

            $period = sprintf("%s - %s", $start->format('d-m-Y'), $end->format('d-m-Y'));
            $exportedAt = now($tz)->format('d-m-Y H:i:s');

            // CSV / XLS: include simple cover lines then header + rows
            if ($fmt === 'csv' || $fmt === 'xls') {
                $callback = function() use ($all, $period, $exportedAt) {
                    $out = fopen('php://output', 'w');
                    // cover lines
                    fputcsv($out, ['Sales Report']);
                    fputcsv($out, ["Periode: $period"]);
                    fputcsv($out, ["UD KASEMI"]);
                    fputcsv($out, ["Dicetak: $exportedAt"]);
                    fputcsv($out, []);
                    // header (remove Cost Now and Price Now)
                    fputcsv($out, ['Sale Code','Sale Date','Item','Qty','Unit','Cost at Sale','Price at Sale','Profit']);
                    foreach ($all as $r) {
                        $unitFactor = (float) ($r->unit_factor ?? 1);
                        $baseQty = (float)$r->qty * $unitFactor;
                        $costAtSale = (float) $r->cost_at_sale;
                        $priceAtSale = (float) $r->price_at_sale;
                        // profit now: price at sale - cost at sale
                        $profitNow = $priceAtSale - $costAtSale;
                        fputcsv($out, [
                            $r->sale_code,
                            \Carbon\Carbon::parse($r->sale_date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i'),
                            $r->item_name,
                            $r->qty,
                            $r->unit_code ?? $r->unit_name,
                            $costAtSale,
                            $priceAtSale,
                            $profitNow
                        ]);
                    }
                    fclose($out);
                };

                $headers = [
                    'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ];

                return response()->stream($callback, 200, $headers);
            }

            if ($fmt === 'pdf') {
                // build table html for PDF (without Cost Now / Price Now)
                $rowsHtml = '<table><thead><tr><th>Sale Code</th><th>Sale Date</th><th>Item</th><th>Qty</th><th>Unit</th><th>Cost at Sale</th><th>Price at Sale</th><th>Profit</th></tr></thead><tbody>';
                foreach ($all as $r) {
                    $unitFactor = (float) ($r->unit_factor ?? 1);
                    $baseQty = (float)$r->qty * $unitFactor;
                    $costAtSale = (float) $r->cost_at_sale;
                    $priceAtSale = (float) $r->price_at_sale;
                    $profitNow = $priceAtSale - $costAtSale;
                    $rowsHtml .= '<tr><td>'.e($r->sale_code).'</td><td>'.e(\Carbon\Carbon::parse($r->sale_date)->setTimezone(config('app.timezone'))->format('d-m-Y H:i')).'</td><td>'.e($r->item_name).'</td><td>'.e($r->qty).'</td><td>'.e($r->unit_code ?? $r->unit_name).'</td><td>'.e(number_format($costAtSale,2)).'</td><td>'.e(number_format($priceAtSale,2)).'</td><td>'.e(number_format($profitNow,2)).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Sales Report Export', 'html' => $rowsHtml, 'cover' => ['company' => 'UD KASEMI','period' => $period, 'exported_at' => $exportedAt]]);
            }
        }

        // aggregated totals for the selected range (not just the page)
        $totalsQuery = DB::table('sale_items as si')
            ->join('sales as s','si.sale_id','s.id')
            ->join('items as it','si.item_id','it.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id','=','si.item_id')->on('iu.unit_id','=','si.unit_id');
            })
            ->whereBetween('s.created_at', [$start, $end])
            ->selectRaw(implode(',', [
                'COALESCE(SUM(COALESCE(si.cost_total, (si.cost_price * si.qty * COALESCE(si.unit_factor, iu.factor,1)))),0) as total_cost_at_sale',
                'COALESCE(SUM(si.line_total),0) as total_price_at_sale',
                'COALESCE(SUM(it.cost_price * si.qty * COALESCE(si.unit_factor, iu.factor,1)),0) as total_cost_now',
                'COALESCE(SUM(COALESCE(iu.price, it.sell_price * COALESCE(si.unit_factor, iu.factor,1)) * si.qty),0) as total_price_now'
            ]));

        $totals = $totalsQuery->first();
        $totalCostAtSale = $totals->total_cost_at_sale ?? 0;
        $totalPriceAtSale = $totals->total_price_at_sale ?? 0;
        $totalProfitNow = $totalPriceAtSale - $totalCostAtSale;

        return view('admin.reports.sales_report', compact(
            'rows','start','end',
            'totalCostAtSale','totalPriceAtSale','totalProfitNow'
        ));
    }

    public function stockReport(Request $request)
    {
        $tz = config('app.timezone');
        $from = $request->input('from');
        $to = $request->input('to');

        // default to current month
        $start = $from ? \Carbon\Carbon::parse($from, $tz)->startOfDay() : \Carbon\Carbon::now($tz)->startOfMonth();
        $end = $to ? \Carbon\Carbon::parse($to, $tz)->endOfDay() : \Carbon\Carbon::now($tz)->endOfMonth();

        $startSql = $start->toDateTimeString();
        $endSql = $end->toDateTimeString();
        // Build per-item aggregates: stock_in (date range), stock_out (date range), remaining (all-time)
        $stockInSub = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id', '=', 'sml.item_id')->on('iu.unit_id', '=', 'sml.unit_id');
            })
                // stock movement lines already store line qty in base units, use qty directly
                ->selectRaw('sml.item_id, COALESCE(SUM(sml.qty),0) as stock_in')
            ->whereBetween('sm.created_at', [$startSql, $endSql])
            ->where('sm.type', 'in')
            ->groupBy('sml.item_id');

        // Use stock_movements (type = 'sale') + stock_movement_lines for OUT movements
        $stockOutSub = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id', '=', 'sml.item_id')->on('iu.unit_id', '=', 'sml.unit_id');
            })
                // use absolute qty for OUT (lines may be stored negative for sales)
                ->selectRaw('sml.item_id, COALESCE(SUM(ABS(sml.qty)),0) as stock_out')
            ->whereBetween('sm.created_at', [$startSql, $endSql])
            ->where('sm.type', 'sale')
            ->groupBy('sml.item_id');

        // Prior period (before the start) totals per item
        $priorInSub = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->selectRaw('sml.item_id, COALESCE(SUM(sml.qty),0) as prior_in')
            ->where('sm.type', 'in')
            ->where('sm.created_at', '<', $startSql)
            ->groupBy('sml.item_id');

        $priorOutSub = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->selectRaw('sml.item_id, COALESCE(SUM(ABS(sml.qty)),0) as prior_out')
            ->where('sm.type', 'sale')
            ->where('sm.created_at', '<', $startSql)
            ->groupBy('sml.item_id');

        $allInSub = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id', '=', 'sml.item_id')->on('iu.unit_id', '=', 'sml.unit_id');
            })
                ->selectRaw('sml.item_id, COALESCE(SUM(sml.qty),0) as total_in')
            ->where('sm.type', 'in')
            ->groupBy('sml.item_id');

        $allOutSub = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id', '=', 'sml.item_id')->on('iu.unit_id', '=', 'sml.unit_id');
            })
                ->selectRaw('sml.item_id, COALESCE(SUM(ABS(sml.qty)),0) as total_out')
            ->where('sm.type', 'sale')
            ->groupBy('sml.item_id');

        $query = DB::table('items as it')
            ->leftJoin('categories as c', 'it.category_id', '=', 'c.id')
            ->leftJoinSub($stockInSub, 'sin', 'sin.item_id', 'it.id')
            ->leftJoinSub($stockOutSub, 'sout', 'sout.item_id', 'it.id')
            ->leftJoinSub($priorInSub, 'previn', 'previn.item_id', 'it.id')
            ->leftJoinSub($priorOutSub, 'prevout', 'prevout.item_id', 'it.id')
            ->leftJoinSub($allInSub, 'allin', 'allin.item_id', 'it.id')
            ->leftJoinSub($allOutSub, 'allout', 'allout.item_id', 'it.id')
            ->selectRaw(implode(',', [
                'it.id as item_id',
                'it.name as item_name',
                'c.name as category_name',
                'COALESCE(previn.prior_in,0) as prior_in',
                'COALESCE(prevout.prior_out,0) as prior_out',
                'COALESCE(sin.stock_in,0) as stock_in',
                'COALESCE(sout.stock_out,0) as stock_out',
                '((COALESCE(previn.prior_in,0) - COALESCE(prevout.prior_out,0)) + (COALESCE(sin.stock_in,0) - COALESCE(sout.stock_out,0))) as remaining'
            ]))
            ->orderBy('it.name');

        $rows = $query->paginate(50)->withQueryString();

        // If export requested, return full filtered dataset (no pagination)
        if ($request->has('export')) {
            $fmt = $request->input('export'); // 'csv','xls' or 'pdf'
            $all = $query->get();
            $filename = 'stock_report_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');

            $period = sprintf("%s - %s", $start->format('d-m-Y'), $end->format('d-m-Y'));
            $exportedAt = now($tz)->format('d-m-Y H:i:s');

            if ($fmt === 'csv' || $fmt === 'xls') {
                $callback = function() use ($all, $period, $exportedAt) {
                    $out = fopen('php://output', 'w');
                    // cover lines
                    fputcsv($out, ['Stock Report']);
                    fputcsv($out, ["Periode: $period"]);
                    fputcsv($out, ["UD KASEMI"]);
                    fputcsv($out, ["Dicetak: $exportedAt"]);
                    fputcsv($out, []);
                    // header
                    fputcsv($out, ['Item','Category','Prior In','Prior Out','Stock In','Stock Out','Remaining']);
                    foreach ($all as $r) {
                        fputcsv($out, [
                            $r->item_name,
                            $r->category_name,
                            $r->prior_in,
                            $r->prior_out,
                            $r->stock_in,
                            $r->stock_out,
                            $r->remaining
                        ]);
                    }
                    fclose($out);
                };

                $headers = [
                    'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ];

                return response()->stream($callback, 200, $headers);
            }

            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>Item</th><th>Category</th><th>Prior In</th><th>Prior Out</th><th>Stock In</th><th>Stock Out</th><th>Remaining</th></tr></thead><tbody>';
                foreach ($all as $r) {
                    $rowsHtml .= '<tr><td>'.e($r->item_name).'</td><td>'.e($r->category_name).'</td><td>'.e(number_format($r->prior_in,2)).'</td><td>'.e(number_format($r->prior_out,2)).'</td><td>'.e(number_format($r->stock_in,2)).'</td><td>'.e(number_format($r->stock_out,2)).'</td><td>'.e(number_format($r->remaining,2)).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';

                return view('admin.exports.printable_table', ['title' => 'Stock Report Export', 'html' => $rowsHtml, 'cover' => ['company' => 'UD KASEMI','period' => $period, 'exported_at' => $exportedAt]]);
            }
        }

        // Totals for the selected range
        $totalStockIn = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id', '=', 'sml.item_id')->on('iu.unit_id', '=', 'sml.unit_id');
            })
            ->whereBetween('sm.created_at', [$startSql, $endSql])
            ->where('sm.type', 'in')
                ->selectRaw('COALESCE(SUM(sml.qty),0) as total')
            ->value('total');
        $totalStockOut = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->leftJoin('item_units as iu', function($join){
                $join->on('iu.item_id', '=', 'sml.item_id')->on('iu.unit_id', '=', 'sml.unit_id');
            })
            ->whereBetween('sm.created_at', [$startSql, $endSql])
            ->where('sm.type', 'sale')
                ->selectRaw('COALESCE(SUM(ABS(sml.qty)),0) as total')
            ->value('total');

        // Prior totals (before the range) for footer remaining calculation
        $totalPriorIn = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->where('sm.type', 'in')
            ->where('sm.created_at', '<', $startSql)
            ->selectRaw('COALESCE(SUM(sml.qty),0) as total')
            ->value('total');
        $totalPriorOut = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->where('sm.type', 'sale')
            ->where('sm.created_at', '<', $startSql)
            ->selectRaw('COALESCE(SUM(ABS(sml.qty)),0) as total')
            ->value('total');

        // Overall remaining (all-time)
        $allStockIn = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->where('sm.type', 'in')
            ->selectRaw('COALESCE(SUM(sml.qty),0) as total')
            ->value('total');
        $allStockOut = DB::table('stock_movement_lines as sml')
            ->join('stock_movements as sm', 'sml.stock_movement_id', '=', 'sm.id')
            ->where('sm.type', 'sale')
                ->selectRaw('COALESCE(SUM(ABS(sml.qty)),0) as total')
            ->value('total');
        // Remaining at end of selected range = prior remaining + net movements in range
        $totalRemaining = (($totalPriorIn ?? 0) - ($totalPriorOut ?? 0)) + (($totalStockIn ?? 0) - ($totalStockOut ?? 0));

        return view('admin.reports.stock_report', [
            'rows' => $rows,
            'start' => $start,
            'end' => $end,
            'totalStockIn' => $totalStockIn,
            'totalStockOut' => $totalStockOut,
            'totalRemaining' => $totalRemaining,
        ]);
    }

}
