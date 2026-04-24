<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,inventory')->only(['index']);
        $this->middleware('admin')->except(['index']);
    }
    public function index(Request $request)
    {
        $query = Unit::query()->orderBy('id','desc');
        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'units_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');
            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Description</th></tr></thead><tbody>';
                foreach ($all as $u) {
                    $rowsHtml .= '<tr><td>'.e($u->id).'</td><td>'.e($u->name).'</td><td>'.e($u->code).'</td><td>'.e($u->description).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Units Export', 'html' => $rowsHtml]);
            }
            $callback = function() use ($all) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Name','Code','Description']);
                foreach ($all as $u) fputcsv($out, [$u->id, $u->name, $u->code, $u->description]);
                fclose($out);
            };
            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream($callback, 200, $headers);
        }

        $units = $query->paginate(20);
        return view('admin.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.units.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        Unit::create($data);
        return redirect()->route('admin.units.index')->with('success','Unit created');
    }

    public function edit(Unit $unit)
    {
        return view('admin.units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:units,code,' . $unit->id,
            'description' => 'nullable|string',
        ]);

        $unit->update($data);
        return redirect()->route('admin.units.index')->with('success','Unit updated');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('admin.units.index')->with('success','Unit deleted');
    }
}
