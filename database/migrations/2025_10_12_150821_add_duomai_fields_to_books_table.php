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
        Schema::table('books', function (Blueprint $table) {
            $table->decimal('coupon_price', 8, 2)->nullable()->after('commission_amount')->comment('优惠券价格');
            $table->decimal('final_price', 8, 2)->nullable()->after('coupon_price')->comment('最终价格');
            $table->string('seller_name')->nullable()->after('final_price')->comment('商家名称');
            $table->decimal('good_comment_rate', 5, 2)->nullable()->after('seller_name')->comment('好评率');
            $table->integer('comments_count')->default(0)->after('good_comment_rate')->comment('评论数量');
            $table->string('item_id')->nullable()->after('comments_count')->comment('商品ID');
            $table->string('coupon_url')->nullable()->after('item_id')->comment('优惠券链接');
            $table->decimal('coupon_quota', 8, 2)->nullable()->after('coupon_url')->comment('优惠券额度');
            $table->timestamp('coupon_start_time')->nullable()->after('coupon_quota')->comment('优惠券开始时间');
            $table->timestamp('coupon_end_time')->nullable()->after('coupon_start_time')->comment('优惠券结束时间');
            
            // 添加索引
            $table->index('coupon_price');
            $table->index('final_price');
            $table->index('good_comment_rate');
            $table->index('comments_count');
            $table->index('item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'coupon_price',
                'final_price',
                'seller_name',
                'good_comment_rate',
                'comments_count',
                'item_id',
                'coupon_url',
                'coupon_quota',
                'coupon_start_time',
                'coupon_end_time'
            ]);
        });
    }
};
