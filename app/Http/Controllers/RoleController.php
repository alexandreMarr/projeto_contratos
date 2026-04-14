<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view roles')->only(['index', 'show']);
        $this->middleware('permission:create roles')->only(['create', 'store']);
        $this->middleware('permission:edit roles')->only(['edit', 'update', 'assignPermissions']);
        $this->middleware('permission:delete roles')->only(['destroy']);
    }

    public function index()
    {
        $roles = Role::withCount('permissions')->paginate(12);
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissionGroups = config('permissions');
        return view('admin.roles.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles'],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        if ($request->filled('permissions')) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.edit', $role)
            ->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        $permissionGroups = config('permissions');

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions', 'permissionGroups'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles')->ignore($role->id)],
        ]);

        $role->update(['name' => $request->name]);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Perfil atualizado com sucesso.');
    }

    public function assignPermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $role->syncPermissions($permissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        activity('Perfil de Acesso (Role)')
            ->performedOn($role)
            ->causedBy(auth()->user())
            ->withProperties(['permissions' => $permissions->pluck('name')->toArray()])
            ->log("Permissões do perfil '{$role->name}' foram atualizadas");

        return redirect()->route('admin.roles.edit', $role)
            ->with('success', 'Permissões do perfil atualizadas com sucesso.');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            return redirect()->route('admin.roles.index')
                ->with('error', 'O perfil admin não pode ser removido.');
        }

        $role->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('admin.roles.index')
            ->with('success', 'Perfil removido com sucesso.');
    }
}
