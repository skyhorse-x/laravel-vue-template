<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'status'])]
class Role extends Model
{
    use HasFactory;

    protected $table = 'luma_roles';

    protected function casts(): array
    {
        return [
            'status' => 'integer',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'luma_role_permissions', 'role_id', 'permission_id');
    }

    public function admins(): HasMany
    {
        return $this->hasMany(Admin::class, 'role_id');
    }
}
