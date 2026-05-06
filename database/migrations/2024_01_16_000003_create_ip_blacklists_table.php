<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_ip_blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 50)->unique()->comment('IP地址或网段');
            $table->string('type', 50)->default('block')->comment('类型: block-ban封禁 login-登录限制');
            $table->string('reason', 500)->nullable()->comment('封禁原因');
            $table->timestamp('expired_at')->nullable()->comment('过期时间，为空表示永久');
            $table->unsignedBigInteger('operator_id')->default(0)->comment('操作人ID');
            $table->string('operator_name', 100)->nullable()->comment('操作人');
            $table->timestamp('created_at')->useCurrent();

            $table->index('type');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_ip_blacklists');
    }
};
