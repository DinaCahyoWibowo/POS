<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockOpname;
use App\Models\StockOpnameLine;
use App\Models\Item;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StockOpnameController extends Controller
{
    public function index()
    {
        $opnames = StockOpname::withCount('lines')->orderBy('opname_date','desc')->paginate(20);
        return view('admin.stock_opnames.index', compact('opnames'));
    }

    public function create()
    {
        $items = Item::orderBy('name')->get();
        return view('admin.stock_opnames.create', compact('items'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opname_date' => 'required|date',
            'lines' => 'required|array|min:1',
            'lines.*.item_id' => 'required|exists:items,id',
            'lines.*.physical_qty' => 'nullable|numeric',
            'lines.*.reason' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // generate sequential daily code: OPN + YYYYMMDD + 4-digit sequence (resets each day)
            $tz = config('app.timezone');
            $todayDate = \Carbon\Carbon::now($tz)->toDateString();
            $todayYmd = \Carbon\Carbon::now($tz)->format('Ymd');
            $maxTries = 5;
            $tries = 0;
            $opname = null;

            do {
                // recompute count each try in case another opname was created meanwhile
                $dailyCount = StockOpname::whereDate('created_at', $todayDate)->count();
                $code = 'OPN'.$todayYmd.str_pad($dailyCount + 1 + $tries, 4, '0', STR_PAD_LEFT);
                try {
                    $opname = StockOpname::create(['code' => $code, 'opname_date' => $data['opname_date'], 'reason' => $request->input('reason') ?? null]);
                    break; // success
                } catch (\Illuminate\Database\QueryException $ex) {
                    // duplicate key or other DB error — retry a few times on duplicate
                    $sqlState = $ex->getCode();
                    if ($sqlState == '23000' || strpos($ex->getMessage(), 'Duplicate') !== false) {
                        $tries++;
                        if ($tries >= $maxTries) throw $ex;
                        usleep(10000);
                        continue;
                    }
                    throw $ex;
                }
            } while ($tries < $maxTries);

            $sm = null;
            $movementLines = [];

            foreach ($data['lines'] as $ln) {
                // skip lines without a provided physical qty
                if (!isset($ln['physical_qty']) || $ln['physical_qty'] === '' ) continue;
                $item = Item::find($ln['item_id']);
                $systemQty = $item ? $item->currentStock() : 0;
                $physical = (float) $ln['physical_qty'];
                $difference = $physical - (float)$systemQty;

                StockOpnameLine::create([
                    'stock_opname_id' => $opname->id,
                    'item_id' => $ln['item_id'],
                    'system_qty' => $systemQty,
                    'physical_qty' => $physical,
                    'difference' => $difference,
                    'reason' => $ln['reason'] ?? null,
                ]);

                if ($difference != 0) {
                    // lazily create stock movement for opname adjustments
                    if (!$sm) {
                        $sm = StockMovement::create([
                            'type' => 'opname',
                            'reference_type' => StockOpname::class,
                            'reference_id' => $opname->id,
                            'note' => 'Opname '.$opname->code,
                            'created_by' => auth()->id() ?? null,
                        ]);
                    }

                    $movementLines[] = [
                        'stock_movement_id' => $sm->id,
                        'item_id' => $item->id,
                        'item_name' => $item->name,
                        'unit_id' => $item->base_unit_id ?? null,
                        'factor' => 1,
                        'qty' => $difference,
                        'cost_price' => $item->cost_price ?? 0,
                    ];
                }
            }

            // persist movement lines if any
            if ($sm && count($movementLines) > 0) {
                foreach ($movementLines as $ml) {
                    StockMovementLine::create($ml);
                }
            }

            DB::commit();
            return redirect()->route('admin.stock_opnames.index')->with('success','Stock opname created and stock adjusted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(StockOpname $stock_opname)
    {
        $stock_opname->load('lines.item');
        return view('admin.stock_opnames.show', ['opname' => $stock_opname]);
    }
}
