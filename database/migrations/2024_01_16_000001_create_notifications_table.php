<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->default(0)->comment('接收用户ID，0为全体用户');
            $table->string('type', 50)->comment('通知类型: system/order/article/message');
            $table->string('title', 200)->comment('通知标题');
            $table->text('content')->nullable()->comment('通知内容');
            $table->string('link', 500)->nullable()->comment('跳转链接');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读: 0-未读 1-已读');
            $table->timestamp('read_at')->nullable()->comment('阅读时间');
            $table->timestamps();

            $table->index(['user_id', 'is_read']);
            $table->index('type');
        });

        Schema::create('luma_notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique()->comment('模板代码');
            $table->string('name', 200)->comment('模板名称');
            $table->string('type', 50)->comment('通知类型');
            $table->string('subject', 200)->nullable()->comment('邮件主题');
            $table->text('content')->comment('通知内容模板');
            $table->json('variables')->nullable()->comment('变量列表');
            $table->tinyInteger('status')->default(1)->comment('状态: 0-禁用 1-启用');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_notifications');
        Schema::dropIfExists('luma_notification_templates');
    }
};
