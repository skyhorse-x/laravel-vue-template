<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\OperationLog;
use Illuminate\Support\Facades\Log;

class CleanOldLogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $days;

    public function __construct(int $days = 30)
    {
        $this->days = $days;
    }

    public function handle(): void
    {
        $beforeDate = now()->subDays($this->days)->format('Y-m-d H:i:s');
        $count = OperationLog::where('created_at', '<', $beforeDate)->delete();
        Log::info("Cleaned {$count} operation logs older than {$this->days} days");
    }
}
