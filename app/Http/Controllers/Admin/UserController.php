<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $query = User::query()->select('users.*');

        // Search
        if ($q = request()->query('q')) {
            $query->where(function ($q2) use ($q) {
                $q2->where('name', 'like', "%{$q}%")
                   ->orWhere('username', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        // Filter by role slug
        if ($role = request()->query('role')) {
            $query->where('role', $role);
        }

        // Sorting
        $allowedSorts = ['id','name','username','email','role'];
        $sort = request()->query('sort', 'id');
        $direction = strtolower(request()->query('direction', 'desc')) === 'asc' ? 'asc' : 'desc';

        if (! in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        // If sorting by role, join roles table to order by roles.name
        if ($sort === 'role') {
            $query->leftJoin('roles', 'roles.slug', '=', 'users.role')
                  ->orderBy('roles.name', $direction)
                  ->select('users.*');
        } else {
            $query->orderBy('users.' . $sort, $direction);
        }

        // support server-side export of filtered users
        if (request()->has('export')) {
            $fmt = request()->input('export');
            $all = $query->get();
            $filename = 'users_' . now()->format('Ymd_His') . (($fmt === 'xls') ? '.xls' : '.csv');
            if ($fmt === 'pdf') {
                $rowsHtml = '<table><thead><tr><th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th></tr></thead><tbody>';
                foreach ($all as $u) {
                    $rowsHtml .= '<tr><td>'.e($u->id).'</td><td>'.e($u->name).'</td><td>'.e($u->username).'</td><td>'.e($u->email).'</td><td>'.e($u->role_name ?? $u->role).'</td></tr>';
                }
                $rowsHtml .= '</tbody></table>';
                return view('admin.exports.printable_table', ['title' => 'Users Export', 'html' => $rowsHtml]);
            }
            $callback = function() use ($all) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['ID','Name','Username','Email','Role']);
                foreach ($all as $u) fputcsv($out, [$u->id, $u->name, $u->username, $u->email, $u->role_name ?? $u->role]);
                fclose($out);
            };
            $headers = [
                'Content-Type' => ($fmt === 'xls') ? 'application/vnd.ms-excel' : 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];
            return response()->stream($callback, 200, $headers);
        }

        $users = $query->paginate(15)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'required|string|exists:roles,slug',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role' => $request->input('role', 'user'),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string|exists:roles,slug',
        ]);

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->role = $request->input('role', 'user');
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        // Prevent deleting self
        if (auth()->id() === $user->id) {
            return redirect()->route('admin.users.index')->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (! is_array($ids) || empty($ids)) {
            return redirect()->route('admin.users.index')->with('error', 'No users selected.');
        }

        // Prevent deleting current user
        $ids = array_filter($ids, function ($id) {
            return (int) $id !== auth()->id();
        });

        User::whereIn('id', $ids)->delete();

        return redirect()->route('admin.users.index')->with('success', 'Selected users deleted.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'Password updated successfully.');
    }
}
