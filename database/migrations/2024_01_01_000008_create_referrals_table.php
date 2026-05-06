<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referrer_id')->constrained('luma_users')->onDelete('cascade')->comment('推荐人');
            $table->foreignId('referee_id')->constrained('luma_users')->onDelete('cascade')->comment('被推荐人');
            $table->decimal('bonus', 10, 2)->default(0)->comment('奖励金额');
            $table->tinyInteger('status')->default(0)->comment('0未完成 1已完成');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_referrals');
    }
};
