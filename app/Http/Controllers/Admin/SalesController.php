<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,sales')->only(['index','show','receipt']);
        $this->middleware('admin')->except(['index','show','receipt']);
    }
    public function index(Request $request)
    {
        $query = Sale::with(['items.item','payments'])->orderBy('created_at','desc');

        // optional date range filtering (expects YYYY-MM-DD inputs)
        $start = $request->query('start_date');
        $end = $request->query('end_date');
        if($start){
            try{ $s = Carbon::createFromFormat('Y-m-d', $start)->startOfDay(); $query->where('created_at', '>=', $s); } catch(\Exception $e){}
        }
        if($end){
            try{ $e = Carbon::createFromFormat('Y-m-d', $end)->endOfDay(); $query->where('created_at', '<=', $e); } catch(\Exception $e){}
        }

        // support server-side export of filtered rows
        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'sales_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');
            $callback = function() use ($all){
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Code','Date','Items','Total','Payments','Change','Status']);
                foreach ($all as $s) {
                    $payments = $s->payments->sum('amount');
                    $change = max(0, $payments - ($s->total ?? 0));
                    fputcsv($out, [ $s->id, $s->code, $s->created_at->format('Y-m-d H:i:s'), $s->items->count(), $s->total, $payments, $change, $s->status ]);
                }
                fclose($out);
            };
            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Code</th><th>Date</th><th>Items</th><th>Total</th><th>Payments</th><th>Change</th><th>Status</th></tr></thead><tbody>';
                foreach ($all as $s) {
                    $payments = $s->payments->sum('amount');
                    $change = max(0, $payments - ($s->total ?? 0));
                    $rowsHtml .= '<tr><td>'.e($s->id).'</td><td>'.e($s->code).'</td><td>'.e($s->created_at->format('Y-m-d H:i:s')).'</td><td>'.e($s->items->count()).'</td><td>'.e($s->total).'</td><td>'.e($payments).'</td><td>'.e($change).'</td><td>'.e($s->status).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Sales Export', 'html' => $rowsHtml]);
            }
            return response()->stream($callback, 200, $headers);
        }

        $sales = $query->paginate(25)->withQueryString();
        return view('admin.sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['items.item','payments']);
        return view('admin.sales.show', compact('sale'));
    }

    /**
     * Return receipt partial (no layout) for AJAX embedding on POS page.
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['items.item','payments']);
        return view('admin.sales.receipt_partial', compact('sale'));
    }
}
