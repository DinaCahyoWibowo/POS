<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::orderBy('id', 'desc');
        if ($request->has('export')) {
            $fmt = $request->input('export');
            $all = $query->get();
            $filename = 'roles_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');
            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Name</th><th>Slug</th><th>Description</th></tr></thead><tbody>';
                foreach ($all as $r) {
                    $rowsHtml .= '<tr><td>'.e($r->id).'</td><td>'.e($r->name).'</td><td>'.e($r->slug).'</td><td>'.e($r->description).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Roles Export', 'html' => $rowsHtml]);
            }
            $callback = function() use ($all) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Name','Slug','Description']);
                foreach ($all as $r) fputcsv($out, [$r->id, $r->name, $r->slug, $r->description]);
                fclose($out);
            };
            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream($callback, 200, $headers);
        }

        $roles = $query->paginate(15);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        // Prevent creating roles in demo mode
        if (session('app_mode') === 'demo') {
            return redirect()->route('admin.roles.index')->with('error', 'Creating new roles is disabled in demo mode.');
        }

        return view('admin.roles.create');
    }

    public function store(Request $request)
    {
        if (session('app_mode') === 'demo') {
            return redirect()->route('admin.roles.index')->with('error', 'Creating new roles is disabled in demo mode.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:roles,slug',
            'description' => 'nullable|string',
        ]);

        Role::create($request->only('name','slug','description'));

        return redirect()->route('admin.roles.index')->with('success','Role created.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|alpha_dash|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
        ]);

        $role->update($request->only('name','slug','description'));

        return redirect()->route('admin.roles.index')->with('success','Role updated.');
    }

    public function destroy(Role $role)
    {
        // Prevent deleting default admin/user roles
        if (in_array($role->slug, ['admin','user'])) {
            return redirect()->route('admin.roles.index')->with('error','Cannot delete default roles.');
        }

        $role->delete();
        return redirect()->route('admin.roles.index')->with('success','Role deleted.');
    }
}
