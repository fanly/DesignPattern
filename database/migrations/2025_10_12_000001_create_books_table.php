<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('图书标题');
            $table->string('author')->nullable()->comment('作者');
            $table->string('publisher')->nullable()->comment('出版社');
            $table->string('isbn')->nullable()->comment('ISBN');
            $table->decimal('price', 8, 2)->nullable()->comment('价格');
            $table->decimal('original_price', 8, 2)->nullable()->comment('原价');
            $table->string('image_url')->nullable()->comment('封面图片');
            $table->string('product_url')->comment('商品链接');
            $table->text('description')->nullable()->comment('描述');
            $table->date('publish_date')->nullable()->comment('出版日期');
            $table->integer('sales_volume')->default(0)->comment('销量');
            $table->decimal('commission_rate', 5, 2)->nullable()->comment('佣金比例');
            $table->decimal('commission_amount', 8, 2)->nullable()->comment('佣金金额');
            $table->string('category')->nullable()->comment('分类');
            $table->timestamp('last_api_call')->nullable()->comment('最后API调用时间');
            $table->timestamps();
            
            $table->index('publish_date');
            $table->index('sales_volume');
            $table->index('last_api_call');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};