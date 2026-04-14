<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ProcessoContratacaoPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'view empresas',
            'create empresas',
            'edit empresas',
            'delete empresas',
            'view processos contratacao',
            'create processos contratacao',
            'edit processos contratacao',
            'delete processos contratacao',
            'manage etapas processos contratacao',
            'view etapas padrao',
            'create etapas padrao',
            'edit etapas padrao',
            'delete etapas padrao',
            'view setores',
            'create setores',
            'edit setores',
            'delete setores',
            'view aditivos',
            'create aditivos',
            'edit aditivos',
            'delete aditivos',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
