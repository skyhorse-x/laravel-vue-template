<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('luma_products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200)->comment('商品名称');
            $table->string('slug', 200)->unique()->comment('商品slug');
            $table->text('description')->nullable()->comment('商品描述');
            $table->text('content')->nullable()->comment('商品详情');
            $table->decimal('price', 10, 2)->default(0)->comment('售价');
            $table->decimal('original_price', 10, 2)->default(0)->comment('原价');
            $table->integer('stock')->default(0)->comment('库存');
            $table->integer('sales')->default(0)->comment('销量');
            $table->string('cover', 500)->nullable()->comment('封面图');
            $table->json('images')->nullable()->comment('图片列表');
            $table->unsignedBigInteger('category_id')->default(0)->comment('分类ID');
            $table->string('category_name', 100)->nullable()->comment('分类名称');
            $table->tinyInteger('status')->default(1)->comment('状态: 0-下架 1-上架');
            $table->tinyInteger('is_featured')->default(0)->comment('是否推荐');
            $table->tinyInteger('is_hot')->default(0)->comment('是否热门');
            $table->tinyInteger('is_new')->default(0)->comment('是否新品');
            $table->integer('sort')->default(0)->comment('排序');
            $table->json('specs')->nullable()->comment('规格列表');
            $table->json('attributes')->nullable()->comment('属性列表');
            $table->timestamps();

            $table->index('category_id');
            $table->index('status');
            $table->index('is_featured');
            $table->index('is_hot');
            $table->index('is_new');
        });

        Schema::create('luma_product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->comment('分类名称');
            $table->string('slug', 100)->unique()->comment('分类slug');
            $table->text('description')->nullable()->comment('分类描述');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父分类ID');
            $table->integer('sort')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态: 0-禁用 1-启用');
            $table->timestamps();

            $table->index('parent_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('luma_products');
        Schema::dropIfExists('luma_product_categories');
    }
};
