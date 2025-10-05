<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Usamamuneerchaudhary\Commentify\Traits\Commentable;

class DesignPattern extends Model
{
    use HasFactory, Commentable;
    
    protected $fillable = [
        'category_id',
        'name_zh',
        'name_en',
        'slug',
        'description_zh',
        'description_en',
        'file_path_zh',
        'file_path_en',
        'is_published',
        'sort_order',
    ];
    
    protected $casts = [
        'is_published' => 'boolean',
    ];
    
    /**
     * 获取该设计模式所属的分类
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PatternCategory::class, 'category_id');
    }
    
    /**
     * 获取设计模式的名称（根据当前语言）
     */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        $nameField = $locale === 'en' ? 'name_en' : 'name_zh';
        
        if (isset($this->attributes[$nameField]) && !empty($this->attributes[$nameField])) {
            return $this->attributes[$nameField];
        }
        
        // 如果当前语言的内容不存在，回退到中文
        return $this->attributes['name_zh'] ?? '未命名';
    }
    
    /**
     * 获取设计模式的描述（根据当前语言）
     */
    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        $descriptionField = $locale === 'en' ? 'description_en' : 'description_zh';
        
        if (isset($this->attributes[$descriptionField]) && !empty($this->attributes[$descriptionField])) {
            return $this->attributes[$descriptionField];
        }
        
        // 如果当前语言的内容不存在，回退到中文
        return $this->attributes['description_zh'] ?? null;
    }
    
    /**
     * 获取设计模式的Markdown内容
     */
    public function getContent(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }
        
        $filePathField = $locale === 'en' ? 'file_path_en' : 'file_path_zh';
        $filePath = $this->attributes[$filePathField] ?? null;
        
        if (!$filePath) {
            // 如果当前语言的文件不存在，回退到中文文件
            $filePath = $this->attributes['file_path_zh'] ?? null;
            
            if (!$filePath) {
                return "# {$this->name}\n\n" . __('Content is being written...');
            }
        }
        
        // 直接使用数据库中的文件路径
        $fullPath = base_path($filePath);
        
        if (!file_exists($fullPath)) {
            return "# {$this->name}\n\n" . __('Content is being written...');
        }
        
        return file_get_contents($fullPath);
    }
    
    /**
     * 获取渲染后的HTML内容
     */
    public function getHtmlContent(): string
    {
        return $this->getContent();
    }
    
    /**
     * 从Markdown内容中提取标题生成目录
     */
    public function getHeadings(): array
    {
        $content = $this->getContent();
        $headings = [];
        
        preg_match_all('/^(#{1,6})\\s+(.+)$/m', $content, $matches);
        
        foreach ($matches[2] as $i => $title) {
            $level = strlen($matches[1][$i]);
            // 清理标题，移除Markdown分隔线等特殊字符
            $cleanTitle = trim($title);
            $cleanTitle = preg_replace('/^-+$/', '', $cleanTitle); // 移除纯分隔线
            $cleanTitle = preg_replace('/^=+$/', '', $cleanTitle); // 移除纯等号线
            
            if (empty($cleanTitle)) {
                continue; // 跳过空标题
            }
            
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]/u', '-', $cleanTitle));
            $slug = preg_replace('/-+/', '-', $slug); // 移除连续的破折号
            $slug = trim($slug, '-'); // 移除首尾的破折号
            
            $headings[] = [
                'level' => $level,
                'title' => $cleanTitle,
                'slug' => $slug,
                'indent' => $level - 1
            ];
        }
        
        return $headings;
    }
    
    /**
     * 保存Markdown内容
     */
    public function saveContent(string $content, string $locale = 'zh'): bool
    {
        $filePathField = $locale === 'zh' ? 'file_path_zh' : 'file_path_en';
        
        if (!$this->$filePathField) {
            $this->$filePathField = "{$this->slug}_{$locale}.md";
            $this->save();
        }
        
        // 直接使用数据库中的文件路径
        $fullPath = base_path($this->$filePathField);
        
        // 确保目录存在
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }
        
        return file_put_contents($fullPath, $content) !== false;
    }
    
    /**
     * 获取所有支持的语言
     */
    public function getSupportedLocales(): array
    {
        $locales = [];
        
        if (!empty($this->name_zh) || !empty($this->file_path_zh)) {
            $locales[] = 'zh';
        }
        
        if (!empty($this->name_en) || !empty($this->file_path_en)) {
            $locales[] = 'en';
        }
        
        return $locales;
    }
}
