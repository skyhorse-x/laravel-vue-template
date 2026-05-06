<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('配置键');
            $table->string('value', 500)->nullable()->comment('配置值');
            $table->string('type', 50)->default('string')->comment('类型:string,number,boolean,array,password');
            $table->string('group', 50)->default('general')->comment('分组');
            $table->string('title', 100)->nullable()->comment('标题');
            $table->text('description')->nullable()->comment('描述');
            $table->integer('sort')->default(0)->comment('排序');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_settings');
    }
};
