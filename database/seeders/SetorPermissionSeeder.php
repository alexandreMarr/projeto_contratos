<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class SetorPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view setores',
            'create setores',
            'edit setores',
            'delete setores',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
