<?php

namespace App\Http\Controllers;

use App\Models\Setor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view users')->only(['index', 'edit']);
        $this->middleware('permission:create users')->only(['create', 'store']);
        $this->middleware('permission:edit users')->only(['edit', 'update']);
        $this->middleware('permission:delete users')->only(['destroy']);
        $this->middleware('permission:assign roles to users')->only(['assignRoles']);
        $this->middleware('permission:assign direct permissions to users')->only(['assignDirectPermissions']);
    }

    public function index()
    {
        $users = User::with(['roles', 'setores'])->paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $setores = Setor::where('ativo', true)->orderBy('nome')->get();
        return view('admin.users.create', compact('roles', 'setores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $roles = Role::whereIn('id', $request->roles ?? [])->get();
        $user->syncRoles($roles);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        activity('Usuário')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties([
                'roles' => $roles->pluck('name')->toArray(),
            ])
            ->log("Usuário '{$user->name}' criado");

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Usuário criado com sucesso. Agora vincule-o aos setores necessários.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $permissionGroups = config('permissions');
        $setores = Setor::with(['usuarios' => function ($q) use ($user) {
            $q->where('users.id', $user->id);
        }])->orderBy('nome')->get();

        return view('admin.users.edit', [
            'user' => $user->load(['roles', 'permissions', 'setores']),
            'roles' => $roles,
            'userRoles' => $user->roles->pluck('id')->toArray(),
            'permissions' => $permissions,
            'userPermissions' => $user->permissions->pluck('id')->toArray(),
            'permissionGroups' => $permissionGroups,
            'setores' => $setores,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        activity('Usuário')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Dados do usuário '{$user->name}' atualizados");

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Dados do usuário atualizados com sucesso.');
    }

    public function assignRoles(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'Você não pode alterar seus próprios perfis de acesso.');
        }

        if ($user->hasRole('admin') && !auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'Você não tem permissão para alterar os perfis de um administrador.');
        }

        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
        ]);

        $roles = Role::whereIn('id', $request->roles ?? [])->get();
        $adminRole = Role::where('name', 'admin')->first();

        if (!auth()->user()->hasRole('admin') && $adminRole && $roles->contains($adminRole)) {
            return redirect()->route('admin.users.edit', $user)
                ->with('error', 'Apenas administradores podem atribuir o perfil de admin.');
        }

        $user->syncRoles($roles);
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        activity('Usuário')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['roles' => $roles->pluck('name')->toArray()])
            ->log("Perfis (Roles) do usuário '{$user->name}' atualizados");

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Perfis (Roles) atribuídos com sucesso.');
    }

    public function assignDirectPermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
        $user->syncPermissions($permissions);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        activity('Usuário')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->withProperties(['permissions' => $permissions->pluck('name')->toArray()])
            ->log("Permissões diretas do usuário '{$user->name}' atualizadas");

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Permissões diretas atribuídas com sucesso.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Você não pode excluir seu próprio usuário.');
        }

        $user->delete();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        activity('Usuário')
            ->performedOn($user)
            ->causedBy(auth()->user())
            ->log("Usuário '{$user->name}' excluído");

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuário excluído com sucesso.');
    }
}
