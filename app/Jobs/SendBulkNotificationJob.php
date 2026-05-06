<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;

class SendBulkNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $userIds;
    public string $type;
    public string $title;
    public string $content;
    public string $link;

    public function __construct(array $userIds, string $type, string $title, string $content = '', string $link = '')
    {
        $this->userIds = $userIds;
        $this->type = $type;
        $this->title = $title;
        $this->content = $content;
        $this->link = $link;
    }

    public function handle(): void
    {
        foreach ($this->userIds as $userId) {
            Notification::send($userId, $this->type, $this->title, $this->content, $this->link);
        }
    }
}
