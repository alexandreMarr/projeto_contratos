<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProcessoContratacaoModuloSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProcessoContratacaoPermissionSeeder::class,
            EmpresaPadraoSeeder::class,
            EtapaTemplateSeeder::class,
        ]);
    }
}
