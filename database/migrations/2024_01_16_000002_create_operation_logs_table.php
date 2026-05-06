<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_operation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->comment('操作人ID');
            $table->string('username', 100)->comment('操作人用户名');
            $table->string('module', 100)->comment('操作模块');
            $table->string('action', 100)->comment('操作动作');
            $table->string('method', 20)->comment('请求方法');
            $table->string('url', 500)->comment('请求URL');
            $table->ipAddress('ip', 50)->nullable()->comment('IP地址');
            $table->text('params')->nullable()->comment('请求参数');
            $table->text('result')->nullable()->comment('返回结果');
            $table->string('user_agent', 500)->nullable()->comment('浏览器UA');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
            $table->index('module');
            $table->index('ip');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_operation_logs');
    }
};
