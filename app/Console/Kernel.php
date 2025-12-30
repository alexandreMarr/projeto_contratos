<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ExecutarComandoArtisanJob;
use App\Models\ProcessamentoLog;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {

            // 1️⃣ Cria o log ANTES
            $log = ProcessamentoLog::create([
                'comando' => 'faturamento:reprocessar-geral',
                'status' => 'iniciado',
                'inicio_execucao' => now(),
                'parametros' => json_encode([
                    'force' => true,
                    'origem' => 'scheduler_diario'
                ]),
            ]);

            // 2️⃣ Dispara o Job
            ExecutarComandoArtisanJob::dispatch(
                'faturamento:reprocessar-geral',
                [
                    '--force'  => true,
                    '--log_id' => $log->id,
                ],
                $log->id
            );

        })
        ->dailyAt('06:00')
        ->name('reprocessamento-geral-diario')
        ->onOneServer()
        ->withoutOverlapping();

    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}
