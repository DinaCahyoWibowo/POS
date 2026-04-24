<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\StockMovement;
use App\Models\StockMovementLine;

class StockMovementController extends Controller
{
    public function __construct()
    {
        // inventory and admin can view/create stock movements; destructive actions remain admin-only
        $this->middleware('role:admin,inventory')->only(['index','create','store','show']);
        $this->middleware('admin')->except(['index','create','store','show']);
    }
    public function create()
    {
        $items = Item::with('itemUnits.unit')->get();
        return view('admin.stock_movements.create', compact('items'));
    }

    public function index(Request $request)
    {
        $query = StockMovement::withCount('lines')->with('lines.item','lines.unit')->orderBy('created_at','desc');

        $from = $request->query('from');
        $to = $request->query('to');
        if ($from) {
            try { $s = \Carbon\Carbon::createFromFormat('Y-m-d', $from)->startOfDay(); $query->where('created_at', '>=', $s); } catch(\Exception $e) {}
        }
        if ($to) {
            try { $e = \Carbon\Carbon::createFromFormat('Y-m-d', $to)->endOfDay(); $query->where('created_at', '<=', $e); } catch(\Exception $e) {}
        }

        // if export requested, stream full filtered dataset
        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'stock_movements_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');

            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Type</th><th>Note</th><th>Item</th><th>Qty</th><th>By</th><th>Date</th></tr></thead><tbody>';
                foreach ($all as $m) {
                    $lines = $m->lines;
                    if ($lines->isEmpty()) {
                        $rowsHtml .= '<tr><td>'.e($m->id).'</td><td>'.e(ucfirst($m->type)).'</td><td>'.e($m->note).'</td><td>-</td><td>0</td><td>'.e(optional($m->createdBy)->name).'</td><td>'.e($m->created_at->format('Y-m-d H:i')).'</td></tr>';
                    } else {
                        foreach ($lines as $line) {
                            $rowsHtml .= '<tr><td>'.e($m->id).'</td><td>'.e(ucfirst($m->type)).'</td><td>'.e($m->note).'</td><td>'.e($line->item_name ?? $line->item?->name).'</td><td>'.e($line->qty).'</td><td>'.e(optional($m->createdBy)->name).'</td><td>'.e($m->created_at->format('Y-m-d H:i')).'</td></tr>';
                        }
                    }
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Stock Movements Export', 'html' => $rowsHtml]);
            }

            $callback = function() use ($all) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Type','Note','Item','Qty','By','Date']);
                foreach ($all as $m) {
                    $lines = $m->lines;
                    if ($lines->isEmpty()) {
                        fputcsv($out, [ $m->id, $m->type, $m->note, '-', 0, optional($m->createdBy)->name, $m->created_at->format('Y-m-d H:i') ] );
                    } else {
                        foreach ($lines as $line) {
                            fputcsv($out, [ $m->id, $m->type, $m->note, $line->item_name ?? $line->item?->name, $line->qty, optional($m->createdBy)->name, $m->created_at->format('Y-m-d H:i') ] );
                        }
                    }
                }
                fclose($out);
            };

            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream($callback, 200, $headers);
        }

        $movements = $query->paginate(25)->withQueryString();
        return view('admin.stock_movements.index', compact('movements'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'unit_id' => 'required|exists:units,id',
            'qty' => 'required|integer|min:1',
            'cost_price' => 'nullable|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        // determine factor from item_units
        $iu = ItemUnit::where('item_id', $data['item_id'])->where('unit_id', $data['unit_id'])->first();
        $factor = $iu ? (float)$iu->factor : 1.0;
        $baseQty = (float)$data['qty'] * $factor;

        $sm = StockMovement::create([
            'type' => 'in',
            'reference_type' => null,
            'reference_id' => null,
            'note' => $data['note'] ?? 'Stock IN',
            'created_by' => auth()->id() ?? null,
        ]);

        StockMovementLine::create([
            'stock_movement_id' => $sm->id,
            'item_id' => $data['item_id'],
            'item_name' => optional(Item::find($data['item_id']))->name,
            'unit_id' => $data['unit_id'],
            'factor' => $factor,
            'qty' => $baseQty,
            'cost_price' => $data['cost_price'] ?? null,
        ]);

        return redirect()->route('admin.items.index')->with('success', 'Stock added');
    }

    public function show(StockMovement $stock_movement)
    {
        $stock_movement->load('lines.item','lines.unit');
        return view('admin.stock_movements.show', ['movement' => $stock_movement]);
    }
}
