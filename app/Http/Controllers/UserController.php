<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->with('role')
            ->when($search = $request->input('search'), function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($role = $request->input('role'), fn($query) => $query->where('role_id', $role))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'roles' => Role::orderBy('name')->get(),
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create', [
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:users,email'],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        User::create($validated);

        return redirect()
            ->route('users.index')
            ->with('success', __('User created successfully.'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            'user' => $user->load('role'),
            'roles' => Role::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', "unique:users,email,{$user->id}"],
            'role_id' => ['required', 'exists:roles,id'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()
            ->route('users.index')
            ->with('success', __('User updated successfully.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        abort_unless(auth()->user()->can('is-admin'), 403, __('Only administrators can delete users.'));

        abort_if($user->id === auth()->id(), 403, __('You cannot delete your own account.'));

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', __('User deleted successfully.'));
    }

    /**
     * Bulk delete multiple users.
     */
    public function bulkDestroy(Request $request)
    {
        abort_unless(auth()->user()->can('is-admin'), 403, __('Only administrators can delete users.'));

        $request->validate([
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'],
        ]);

        $userIds = $request->input('user_ids', []);
        $currentUserId = auth()->id();

        // Filter out current user from deletion
        $userIds = array_filter($userIds, fn($id) => (int) $id !== $currentUserId);

        if (empty($userIds)) {
            return redirect()
                ->route('users.index')
                ->with('error', __('You cannot delete your own account.'));
        }

        $deletedCount = User::whereIn('id', $userIds)->delete();

        return redirect()
            ->route('users.index')
            ->with('success', __(':count user(s) deleted successfully.', ['count' => $deletedCount]));
    }
}
