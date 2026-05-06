<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_user_extends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('luma_users')->onDelete('cascade');
            $table->string('referral_code', 50)->unique()->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->integer('invite_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_user_extends');
    }
};
