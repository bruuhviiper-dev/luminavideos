<?php

namespace App\Console\Commands;

use App\Models\WatchHistory;
use Illuminate\Console\Command;

class ClearOldHistory extends Command
{
    protected $signature = 'tubiii:clear-old-history {days=30 : Número de dias para manter no histórico}';

    protected $description = 'Limpar histórico de vídeos assistidos com mais de X dias';

    public function handle()
    {
        $days = (int) $this->argument('days');
        $cutoffDate = now()->subDays($days);

        $deletedCount = WatchHistory::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Histórico deletado: {$deletedCount} registros removidos (com mais de {$days} dias).");
    }
}

