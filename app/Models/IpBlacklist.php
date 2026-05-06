<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class IpBlacklist extends Model
{
    use HasFactory;

    protected $table = 'luma_ip_blacklists';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'expired_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public static function isBlocked(string $ip, string $type = 'block'): bool
    {
        return self::where('ip', $ip)
            ->where('type', $type)
            ->where(function ($q) {
                $q->whereNull('expired_at')
                    ->orWhere('expired_at', '>', Carbon::now());
            })
            ->exists();
    }

    public static function block(string $ip, string $type, ?string $reason, ?Carbon $expiredAt = null, ?int $operatorId = null, ?string $operatorName = null)
    {
        return self::updateOrCreate(
            ['ip' => $ip, 'type' => $type],
            [
                'reason' => $reason,
                'expired_at' => $expiredAt,
                'operator_id' => $operatorId ?? 0,
                'operator_name' => $operatorName,
                'created_at' => Carbon::now(),
            ]
        );
    }

    public static function unblock(string $ip, string $type = 'block')
    {
        return self::where('ip', $ip)->where('type', $type)->delete();
    }
}
