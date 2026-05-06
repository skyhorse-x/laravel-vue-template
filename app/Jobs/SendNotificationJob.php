<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;

class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $userId;
    public string $type;
    public string $title;
    public string $content;
    public string $link;

    public function __construct(int $userId, string $type, string $title, string $content = '', string $link = '')
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->content = $content;
        $this->link = $link;
    }

    public function handle(): void
    {
        Notification::send($this->userId, $this->type, $this->title, $this->content, $this->link);
    }
}
