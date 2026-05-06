<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'luma_notifications';

    protected function casts(): array
    {
        return [
            'is_read' => 'integer',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function send($userId, string $type, string $title, string $content = '', string $link = '')
    {
        return self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'is_read' => 0,
        ]);
    }

    public static function sendToAll(string $type, string $title, string $content = '', string $link = '')
    {
        return self::create([
            'user_id' => 0,
            'type' => $type,
            'title' => $title,
            'content' => $content,
            'link' => $link,
            'is_read' => 0,
        ]);
    }
}
