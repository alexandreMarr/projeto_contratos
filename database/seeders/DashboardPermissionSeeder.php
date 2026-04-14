<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class DashboardPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view dashboard',
            'manage dashboard',
            'create dashboard personalizado',
            'edit dashboard personalizado',
            'delete dashboard personalizado',
            'share dashboard personalizado',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
