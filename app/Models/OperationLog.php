<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OperationLog extends Model
{
    use HasFactory;

    protected $table = 'luma_operation_logs';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public static function log(
        int $userId,
        string $username,
        string $module,
        string $action,
        string $method,
        string $url,
        ?string $ip,
        ?array $params = null,
        ?array $result = null,
        ?string $userAgent = null
    ) {
        return self::create([
            'user_id' => $userId,
            'username' => $username,
            'module' => $module,
            'action' => $action,
            'method' => $method,
            'url' => $url,
            'ip' => $ip,
            'params' => $params ? json_encode($params) : null,
            'result' => $result ? json_encode($result) : null,
            'user_agent' => $userAgent,
            'created_at' => Carbon::now(),
        ]);
    }
}
