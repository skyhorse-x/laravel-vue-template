<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'referral_code', 'balance', 'invite_count'])]
class UserExtend extends Model
{
    use HasFactory;

    protected $table = 'luma_user_extends';

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:2',
            'invite_count' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
