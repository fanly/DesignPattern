<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PatternCategory extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
    ];
    
    /**
     * 获取该分类下的所有设计模式
     */
    public function designPatterns(): HasMany
    {
        return $this->hasMany(DesignPattern::class, 'category_id');
    }
}
