<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ItemUnit;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,inventory')->only(['index','show']);
        $this->middleware('admin')->except(['index','show']);
    }
    public function index(Request $request)
    {
        // eager-load related models including itemUnits and their unit relation
        $query = Item::with(['brand','category','baseUnit','itemUnits.unit'])->orderBy('name');

        // server-side export: if export param is present, return full filtered dataset
        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'items_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');

            $callback = function() use ($all){
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Code','Name','Brand','Category','Base Unit','Stock','Cost','Price']);
                foreach ($all as $it) {
                    $stock = method_exists($it, 'currentStock') ? $it->currentStock() : '';
                    fputcsv($out, [ $it->id, $it->code, $it->name, $it->brand?->name, $it->category?->name, $it->baseUnit?->name, $stock, $it->cost_price, $it->sell_price ]);
                }
                fclose($out);
            };

            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            if ($fmt === 'pdf') {
                // build HTML table for printable PDF-like output
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Code</th><th>Name</th><th>Brand</th><th>Category</th><th>Base Unit</th><th>Stock</th><th>Cost</th><th>Price</th></tr></thead><tbody>';
                foreach ($all as $it) {
                    $stock = method_exists($it, 'currentStock') ? $it->currentStock() : '';
                    $rowsHtml .= '<tr><td>'.e($it->id).'</td><td>'.e($it->code).'</td><td>'.e($it->name).'</td><td>'.e($it->brand?->name).'</td><td>'.e($it->category?->name).'</td><td>'.e($it->baseUnit?->name).'</td><td>'.e($stock).'</td><td>'.e($it->cost_price).'</td><td>'.e($it->sell_price).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Items Export', 'html' => $rowsHtml]);
            }
            return response()->stream($callback, 200, $headers);
        }

        $items = $query->paginate(25)->withQueryString();
        return view('admin.items.index', compact('items'));
    }

    public function create()
    {
        $brands = Brand::all();
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.items.create', compact('brands','categories','units'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'base_unit_id' => 'required|exists:units,id',
            'image' => 'nullable|image|max:2048',
            'item_units' => 'nullable|array',
            'item_units.*.unit_id' => 'nullable|exists:units,id',
            'item_units.*.factor' => 'nullable|numeric|min:0.000001',
            'item_units.*.price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric',
            'sell_price' => 'nullable|numeric',
        ]);

        // handle uploaded image (store on public disk)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $data['image'] = $path;
        }

        // Item model will auto-generate code
        $item = Item::create($data);

        // create base item unit (store base unit price from item sell_price)
        ItemUnit::create([
            'item_id' => $item->id,
            'unit_id' => $data['base_unit_id'],
            'factor' => 1,
            'price' => isset($data['sell_price']) ? $data['sell_price'] : null,
            'is_base' => 1,
        ]);

        // handle additional item units if provided
        if ($request->filled('item_units') && is_array($request->input('item_units'))) {
            foreach ($request->input('item_units') as $iu) {
                if (empty($iu['unit_id'])) continue;
                // skip if same as base unit
                if ((int)$iu['unit_id'] === (int)$data['base_unit_id']) continue;
                $factor = isset($iu['factor']) && $iu['factor'] !== '' ? (float)$iu['factor'] : 1.0;
                // skip invalid or non-positive factors
                if ($factor <= 0) continue;
                ItemUnit::create([
                    'item_id' => $item->id,
                    'unit_id' => $iu['unit_id'],
                    'factor' => $factor,
                    'price' => isset($iu['price']) ? $iu['price'] : null,
                    'is_base' => 0,
                ]);
            }
        }

        return redirect()->route('admin.items.index')->with('success','Item created');
    }

    public function edit(Item $item)
    {
        $brands = Brand::all();
        $categories = Category::all();
        $units = Unit::all();
        return view('admin.items.edit', compact('item','brands','categories','units'));
    }

    public function update(Request $request, Item $item)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'base_unit_id' => 'required|exists:units,id',
            'image' => 'nullable|image|max:2048',
            'item_units' => 'nullable|array',
            'item_units.*.unit_id' => 'nullable|exists:units,id',
            'item_units.*.factor' => 'nullable|numeric|min:0.000001',
            'item_units.*.price' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'cost_price' => 'nullable|numeric',
            'sell_price' => 'nullable|numeric',
        ]);

        // handle image upload: delete previous image if replaced
        if ($request->hasFile('image')) {
            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $request->file('image')->store('items', 'public');
        }

        // do not change code here - it's auto-generated on create
        $item->update($data);

        // update or create base item unit (store base unit price from item sell_price)
        $base = $item->itemUnits()->where('is_base', 1)->first();
        $baseData = [
            'unit_id' => $data['base_unit_id'],
            'factor' => 1,
            'price' => isset($data['sell_price']) ? $data['sell_price'] : null,
            'is_base' => 1,
        ];
        if ($base) {
            $base->update($baseData);
        } else {
            ItemUnit::create(array_merge($baseData, ['item_id' => $item->id]));
        }

        // Process submitted non-base item units: update existing, create new, collect kept ids
        $submitted = $request->input('item_units', []);
        $keptIds = [];
        if (is_array($submitted)) {
            foreach ($submitted as $iu) {
                if (empty($iu['unit_id'])) continue;
                // skip if this unit equals the selected base unit
                if ((int)$iu['unit_id'] === (int)$data['base_unit_id']) continue;

                $factor = isset($iu['factor']) && $iu['factor'] !== '' ? (float)$iu['factor'] : 1.0;
                if ($factor <= 0) continue;

                $price = isset($iu['price']) ? $iu['price'] : null;

                if (!empty($iu['id'])) {
                    $id = (int)$iu['id'];
                    $record = ItemUnit::where('id', $id)->where('item_id', $item->id)->first();
                    if ($record) {
                        $record->update([
                            'unit_id' => $iu['unit_id'],
                            'factor' => $factor,
                            'price' => $price,
                            'is_base' => 0,
                        ]);
                        $keptIds[] = $record->id;
                        continue;
                    }
                }

                // avoid duplicate item_id+unit_id unique constraint: update if exists
                $existing = ItemUnit::where('item_id', $item->id)
                    ->where('unit_id', $iu['unit_id'])
                    ->where('is_base', 0)
                    ->first();
                if ($existing) {
                    $existing->update([
                        'factor' => $factor,
                        'price' => $price,
                        'is_base' => 0,
                    ]);
                    $keptIds[] = $existing->id;
                } else {
                    $new = ItemUnit::create([
                        'item_id' => $item->id,
                        'unit_id' => $iu['unit_id'],
                        'factor' => $factor,
                        'price' => $price,
                        'is_base' => 0,
                    ]);
                    $keptIds[] = $new->id;
                }
            }
        }

        // remove any non-base item_units that were not kept (deleted from form)
        if (!empty($keptIds)) {
            $item->itemUnits()->where('is_base', 0)->whereNotIn('id', $keptIds)->delete();
        } else {
            // no non-base units submitted -> delete all non-base rows
            $item->itemUnits()->where('is_base', 0)->delete();
        }
        return redirect()->route('admin.items.index')->with('success','Item updated');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('admin.items.index')->with('success','Item deleted');
    }
}
