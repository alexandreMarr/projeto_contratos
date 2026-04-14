<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = collect(config('permissions'))
            ->flatMap(fn ($group) => $group['permissions'])
            ->unique()
            ->values();

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $juridicoRole = Role::firstOrCreate(['name' => 'juridico', 'guard_name' => 'web']);
        $suprimentosRole = Role::firstOrCreate(['name' => 'suprimentos', 'guard_name' => 'web']);
        $planejamentoRole = Role::firstOrCreate(['name' => 'planejamento', 'guard_name' => 'web']);
        $obraRole = Role::firstOrCreate(['name' => 'obra', 'guard_name' => 'web']);
        $diretoriaRole = Role::firstOrCreate(['name' => 'diretoria', 'guard_name' => 'web']);

        $adminRole->syncPermissions(Permission::all());

        $rolePermissions = [
            'juridico' => [
                'view processos contratacao',
                'edit processos contratacao',
                'view empresas',
                'view aditivos',
            ],
            'suprimentos' => [
                'view processos contratacao',
                'edit processos contratacao',
                'view empresas',
                'view aditivos',
            ],
            'planejamento' => [
                'view processos contratacao',
                'edit processos contratacao',
                'view empresas',
            ],
            'obra' => [
                'view processos contratacao',
                'view empresas',
            ],
            'diretoria' => [
                'view processos contratacao',
                'edit processos contratacao',
                'view empresas',
                'view aditivos',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->syncPermissions(Permission::whereIn('name', $permissionNames)->get());
            }
        }

        $adminUser = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('123456'),
            ]
        );

        if (!$adminUser->hasRole('admin')) {
            $adminUser->assignRole($adminRole);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
