<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatternCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name_zh',
        'name_en',
        'slug',
        'description_zh',
        'description_en',
        'sort_order',
    ];
    
    /**
     * 获取分类名称（根据当前语言）
     */
    public function getNameAttribute()
    {
        return app()->getLocale() === 'zh' ? $this->name_zh : $this->name_en;
    }
    
    /**
     * 获取分类描述（根据当前语言）
     */
    public function getDescriptionAttribute()
    {
        return app()->getLocale() === 'zh' ? $this->description_zh : $this->description_en;
    }
    
    /**
     * 获取该分类下的所有设计模式
     */
    public function designPatterns(): HasMany
    {
        return $this->hasMany(DesignPattern::class, 'category_id');
    }
}
