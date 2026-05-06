<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 100)->unique()->nullable();
            $table->text('content');
            $table->string('cover', 255)->nullable();
            $table->string('category', 50)->nullable();
            $table->tinyInteger('status')->default(1)->comment('0草稿 1发布');
            $table->integer('view_count')->default(0);
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_articles');
    }
};
