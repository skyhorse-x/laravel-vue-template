<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $table = 'luma_notification_templates';

    protected function casts(): array
    {
        return [
            'variables' => 'array',
            'status' => 'integer',
        ];
    }
}
