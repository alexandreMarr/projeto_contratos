<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class EtapaTemplatePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view etapas padrao',
            'create etapas padrao',
            'edit etapas padrao',
            'delete etapas padrao',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
