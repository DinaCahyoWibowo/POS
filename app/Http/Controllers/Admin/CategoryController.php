<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin,inventory')->only(['index']);
        $this->middleware('admin')->except(['index']);
    }
    public function index(Request $request)
    {
        $query = Category::query()->orderBy('id','desc');
        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'categories_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');
            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Description</th></tr></thead><tbody>';
                foreach ($all as $c) {
                    $rowsHtml .= '<tr><td>'.e($c->id).'</td><td>'.e($c->name).'</td><td>'.e($c->code).'</td><td>'.e($c->description).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Categories Export', 'html' => $rowsHtml]);
            }
            $callback = function() use ($all) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Name','Code','Description']);
                foreach ($all as $c) fputcsv($out, [$c->id, $c->name, $c->code, $c->description]);
                fclose($out);
            };
            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream($callback, 200, $headers);
        }

        $categories = $query->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code',
            'description' => 'nullable|string',
        ]);

        Category::create($data);
        return redirect()->route('admin.categories.index')->with('success','Category created');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:categories,code,' . $category->id,
            'description' => 'nullable|string',
        ]);

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success','Category updated');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success','Category deleted');
    }
}
