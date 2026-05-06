<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['title', 'slug', 'content', 'cover', 'category', 'status', 'view_count', 'sort'])]
class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'luma_articles';

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'view_count' => 'integer',
            'sort' => 'integer',
        ];
    }
}
