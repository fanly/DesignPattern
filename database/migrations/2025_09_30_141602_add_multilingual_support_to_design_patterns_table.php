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
        // 使用原始SQL语句来避免MySQL版本兼容性问题
        DB::statement("ALTER TABLE design_patterns ADD COLUMN name_en VARCHAR(191) NULL AFTER name");
        DB::statement("ALTER TABLE design_patterns ADD COLUMN description_en TEXT NULL AFTER description");
        DB::statement("ALTER TABLE design_patterns ADD COLUMN file_path_en VARCHAR(191) NULL AFTER file_path");
        
        // 复制现有数据到英文字段
        DB::statement("UPDATE design_patterns SET name_en = name, description_en = description, file_path_en = file_path");
        
        // 重命名原有字段为中文字段
        DB::statement("ALTER TABLE design_patterns CHANGE name name_zh VARCHAR(191) NOT NULL");
        DB::statement("ALTER TABLE design_patterns CHANGE description description_zh TEXT NULL");
        DB::statement("ALTER TABLE design_patterns CHANGE file_path file_path_zh VARCHAR(191) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 恢复原有字段名
        DB::statement("ALTER TABLE design_patterns CHANGE name_zh name VARCHAR(191) NOT NULL");
        DB::statement("ALTER TABLE design_patterns CHANGE description_zh description TEXT NULL");
        DB::statement("ALTER TABLE design_patterns CHANGE file_path_zh file_path VARCHAR(191) NULL");
        
        // 删除英文字段
        DB::statement("ALTER TABLE design_patterns DROP COLUMN name_en");
        DB::statement("ALTER TABLE design_patterns DROP COLUMN description_en");
        DB::statement("ALTER TABLE design_patterns DROP COLUMN file_path_en");
    }
};
