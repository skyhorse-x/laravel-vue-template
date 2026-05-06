<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('模板代码');
            $table->string('name', 200)->comment('模板名称');
            $table->string('type', 50)->comment('通知类型: system, order, article, message');
            $table->string('subject', 500)->nullable()->comment('邮件主题');
            $table->text('content')->comment('通知内容');
            $table->json('variables')->nullable()->comment('变量列表');
            $table->tinyInteger('status')->default(1)->comment('状态: 0-禁用 1-启用');
            $table->timestamps();
            $table->index('type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_notification_templates');
    }
};
