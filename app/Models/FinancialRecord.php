<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'type', 'amount', 'title', 'description', 'order_no', 'status'])]
class FinancialRecord extends Model
{
    use HasFactory;

    protected $table = 'luma_financial_records';

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'status' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
