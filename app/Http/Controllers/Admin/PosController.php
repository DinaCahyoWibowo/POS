<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\StockMovement;
use App\Models\StockMovementLine;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,sales')->only(['index','storeSale']);
        $this->middleware('admin')->except(['index','storeSale']);
    }
    public function index()
    {
        $items = Item::with('brand','category','baseUnit','itemUnits.unit')->paginate(50);
        // attach current stock as a simple attribute so the view's JS can read it
        $items->getCollection()->transform(function($it){
            $it->stock = (int) $it->currentStock();
            return $it;
        });
        return view('admin.pos.index', compact('items'));
    }

    public function storeSale(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.unit_id' => 'required|exists:units,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment.amount' => 'required|numeric|min:0',
        ]);

        // Only cash supported for now
        $paymentAmount = $request->input('payment.amount');

        DB::beginTransaction();
        try {
            // calculate totals and check stock
            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $it) {
                $item = Item::find($it['item_id']);
                $iu = ItemUnit::where('item_id', $item->id)->where('unit_id', $it['unit_id'])->first();
                $factor = $iu ? (float)$iu->factor : 1.0;
                $baseQty = (float)$it['qty'] * $factor;

                // check stock (prevent negative)
                if ($item->currentStock() - $baseQty < 0) {
                    DB::rollBack();
                    return response()->json(['error' => 'Insufficient stock for item: '.$item->name], 422);
                }

                $lineTotal = (float)$it['unit_price'] * (float)$it['qty'];
                $subtotal += $lineTotal;
                $costPerBase = (float) $item->cost_price;
                $costPricePerUnit = $costPerBase * $factor; // cost for the sold unit
                $costTotal = $costPricePerUnit * (float)$it['qty'];
                $lines[] = [
                    'item'=>$item,
                    'unit_id'=>$it['unit_id'],
                    'qty'=>$it['qty'],
                    'unit_price'=>$it['unit_price'],
                    'base_qty'=>$baseQty,
                    'factor'=>$factor,
                    'cost_price_per_unit'=>$costPricePerUnit,
                    'cost_total'=>$costTotal,
                    'cost_price_base'=>$costPerBase,
                ];
            }

            // create sale
            // generate daily-resetting sequence: S + YYYYMMDD + 4-digit sequence (resets each day)
            $tz = config('app.timezone');
            // Use app timezone date and count by DATE(created_at) to avoid mismatches between DB/server timezone
            $todayDate = \Carbon\Carbon::now($tz)->toDateString();
            $todayYmd = \Carbon\Carbon::now($tz)->format('Ymd');
            $dailyCount = Sale::whereDate('created_at', $todayDate)->count();
            $sale = null;
            $maxTries = 5;
            $tries = 0;
            // Attempt to create the sale and retry if a duplicate-key error on `code` occurs.
            do {
                // recompute sequence each try (in case another sale was created meanwhile)
                $dailyCount = Sale::whereDate('created_at', $todayDate)->count();
                $code = 'S'.$todayYmd.str_pad($dailyCount + 1 + $tries,4,'0',STR_PAD_LEFT);
                try {
                    $sale = Sale::create([
                        'code'=>$code,
                        'user_id'=>auth()->id() ?? null,
                        'subtotal'=> $subtotal,
                        'total'=> $subtotal,
                        'tax'=>0,
                        'discount'=>0,
                        'status'=>'completed',
                    ]);
                    break; // success
                } catch (\Illuminate\Database\QueryException $ex) {
                    // MySQL duplicate key error SQLSTATE 23000 (error code 1062)
                    $sqlState = $ex->getCode();
                    if ($sqlState == '23000' || strpos($ex->getMessage(), 'Duplicate') !== false) {
                        $tries++;
                        if ($tries >= $maxTries) throw $ex;
                        // small short sleep to reduce chance of immediate conflict (not required for single cashier)
                        usleep(10000);
                        continue;
                    }
                    throw $ex;
                }
            } while ($tries < $maxTries);

            if (!$sale) {
                throw new \Exception('Unable to create sale after multiple attempts.');
            }

            foreach ($lines as $l) {
                SaleItem::create([
                    'sale_id'=>$sale->id,
                    'item_id'=>$l['item']->id,
                    'unit_id'=>$l['unit_id'],
                    'unit_factor'=> $l['factor'],
                    'qty'=>$l['qty'],
                    'unit_price'=>$l['unit_price'],
                    'line_total'=> $l['unit_price'] * $l['qty'],
                    'cost_price'=> $l['cost_price_base'],
                    'cost_price_per_unit' => $l['cost_price_per_unit'],
                    'cost_total' => $l['cost_total'],
                ]);
            }

            $changeAmount = (float)$paymentAmount - (float)$subtotal;
            if($changeAmount < 0) $changeAmount = 0;

            Payment::create([
                'sale_id'=>$sale->id,
                'method'=>'cash',
                'amount'=>$paymentAmount,
                'change'=>$changeAmount,
            ]);

            // create stock movement (out) - reference sale by its code so the origin is clear
            $sm = StockMovement::create([
                'type'=>'sale',
                'reference_type'=>Sale::class,
                'reference_id'=>$sale->id,
                'note'=>'Sale '.$sale->code,
                'created_by'=>auth()->id() ?? null,
            ]);

            foreach ($lines as $l) {
                StockMovementLine::create([
                    'stock_movement_id'=>$sm->id,
                    'item_id'=>$l['item']->id,
                    'item_name'=>$l['item']->name,
                    'unit_id'=>$l['unit_id'],
                    'factor'=> $l['factor'],
                    'qty'=> -1 * $l['base_qty'],
                    'cost_price'=> $l['cost_price_base'],
                ]);
            }

            DB::commit();
            return response()->json(['success'=>true,'sale_id'=>$sale->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error'=>$e->getMessage()], 500);
        }
    }
}
