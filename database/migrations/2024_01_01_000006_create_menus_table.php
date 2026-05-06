<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_menus', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('route', 100)->nullable();
            $table->string('icon', 50)->nullable();
            $table->integer('parent_id')->default(0);
            $table->integer('sort')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->string('module', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_menus');
    }
};
