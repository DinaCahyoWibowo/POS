<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brand;

class BrandController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,inventory')->only(['index']);
        $this->middleware('admin')->except(['index']);
    }
    public function index(Request $request)
    {
        $query = Brand::query()->orderBy('id','desc');

        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'brands_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');
            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Description</th></tr></thead><tbody>';
                foreach ($all as $b) {
                    $rowsHtml .= '<tr><td>'.e($b->id).'</td><td>'.e($b->name).'</td><td>'.e($b->code).'</td><td>'.e($b->description).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Brands Export', 'html' => $rowsHtml]);
            }
            $callback = function() use ($all) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Name','Code','Description']);
                foreach ($all as $b) fputcsv($out, [$b->id, $b->name, $b->code, $b->description]);
                fclose($out);
            };
            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream($callback, 200, $headers);
        }

        $brands = $query->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function create()
    {
        return view('admin.brands.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:brands,code',
            'description' => 'nullable|string',
        ]);

        Brand::create($data);
        return redirect()->route('admin.brands.index')->with('success','Brand created');
    }

    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:brands,code,' . $brand->id,
            'description' => 'nullable|string',
        ]);

        $brand->update($data);
        return redirect()->route('admin.brands.index')->with('success','Brand updated');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success','Brand deleted');
    }
}
