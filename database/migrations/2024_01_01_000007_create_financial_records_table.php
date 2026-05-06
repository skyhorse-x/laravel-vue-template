<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_financial_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('luma_users')->onDelete('cascade');
            $table->string('type', 50)->comment('收入/支出');
            $table->decimal('amount', 12, 2);
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('order_no', 50)->nullable()->unique();
            $table->tinyInteger('status')->default(1)->comment('0失败 1成功');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_financial_records');
    }
};
