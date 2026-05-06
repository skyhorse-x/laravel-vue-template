<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_role_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('luma_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('luma_permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_role_permissions');
    }
};
