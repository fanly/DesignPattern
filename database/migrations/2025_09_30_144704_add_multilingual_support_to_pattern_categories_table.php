<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pattern_categories', function (Blueprint $table) {
            $table->string('name_zh')->nullable()->after('name');
            $table->string('name_en')->nullable()->after('name_zh');
            $table->text('description_zh')->nullable()->after('description');
            $table->text('description_en')->nullable()->after('description_zh');
        });

        // 将现有数据迁移到新字段
        DB::table('pattern_categories')->get()->each(function ($category) {
            DB::table('pattern_categories')
                ->where('id', $category->id)
                ->update([
                    'name_zh' => $category->name,
                    'name_en' => $category->name,
                    'description_zh' => $category->description,
                    'description_en' => $category->description,
                ]);
        });

        // 删除旧字段
        Schema::table('pattern_categories', function (Blueprint $table) {
            $table->dropColumn(['name', 'description']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pattern_categories', function (Blueprint $table) {
            $table->string('name')->nullable();
            $table->text('description')->nullable();
        });

        // 将数据迁移回旧字段
        DB::table('pattern_categories')->get()->each(function ($category) {
            DB::table('pattern_categories')
                ->where('id', $category->id)
                ->update([
                    'name' => $category->name_zh,
                    'description' => $category->description_zh,
                ]);
        });

        Schema::table('pattern_categories', function (Blueprint $table) {
            $table->dropColumn(['name_zh', 'name_en', 'description_zh', 'description_en']);
        });
    }
};
