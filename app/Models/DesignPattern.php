<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
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
     * 获取设计模式的Markdown内容（带缓存）
     */
    public function getContent(?string $locale = null): string
    {
        if (!$locale) {
            $locale = app()->getLocale();
        }
        
        $cacheKey = "pattern_content_{$this->id}_{$locale}";
        $this->addCacheKey($cacheKey);
        
        // 使用较短的缓存时间，避免文件缓存占用过多空间
        return cache()->remember($cacheKey, 1800, function () use ($locale) { // 30分钟
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
        });
    }
    
    /**
     * 获取渲染后的HTML内容
     */
    public function getHtmlContent(): string
    {
        return $this->getContent();
    }
    
    /**
     * 从Markdown内容中提取标题生成目录（带缓存）
     */
    public function getHeadings(): array
    {
        $cacheKey = "pattern_headings_{$this->id}_" . app()->getLocale();
        $this->addCacheKey($cacheKey);
        
        return cache()->remember($cacheKey, 1800, function () { // 30分钟
            $content = $this->getContent();
            $headings = [];
            
            // 改进的正则表达式，支持更多Markdown标题格式
            preg_match_all('/^(#{1,6})\\s+(.+)$/m', $content, $matches, PREG_SET_ORDER);
            
            foreach ($matches as $match) {
                $level = strlen($match[1]);
                $title = trim($match[2]);
                
                // 跳过空标题和分隔线
                if (empty($title) || preg_match('/^[-=*_]{3,}$/', $title)) {
                    continue;
                }
                
                // 移除Markdown格式标记（**、_等）
                $cleanTitle = preg_replace('/[*_`]/', '', $title);
                $cleanTitle = trim($cleanTitle);
                
                if (empty($cleanTitle)) {
                    continue;
                }
                
                // 生成一致的slug（与前端AlpineJS保持一致）
                $slug = $this->generateSlug($cleanTitle);
                
                $headings[] = [
                    'level' => $level,
                    'title' => $cleanTitle,
                    'slug' => $slug,
                    'indent' => max(0, $level - 2) // h1不缩进，h2缩进1级，以此类推
                ];
            }
            
            return $headings;
        });
    }
    
    /**
     * 生成与前端一致的slug
     */
    protected function generateSlug(string $text): string
    {
        // 转换为小写
        $slug = mb_strtolower($text, 'UTF-8');
        
        // 替换非字母数字字符为破折号
        $slug = preg_replace('/[^a-z0-9\x{4e00}-\x{9fa5}]/u', '-', $slug);
        
        // 移除连续的破折号
        $slug = preg_replace('/-+/', '-', $slug);
        
        // 移除首尾的破折号
        $slug = trim($slug, '-');
        
        // 如果slug为空，生成一个基于内容的哈希
        if (empty($slug)) {
            $slug = 'section-' . substr(md5($text), 0, 8);
        }
        
        return $slug;
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
    
    /**
     * 添加缓存键到管理列表
     */
    protected function addCacheKey(string $cacheKey): void
    {
        $patternKeys = Cache::get('pattern_keys', []);
        if (!in_array($cacheKey, $patternKeys)) {
            $patternKeys[] = $cacheKey;
            Cache::put('pattern_keys', $patternKeys, 86400); // 24小时
        }
    }
    
    /**
     * 清除此设计模式的所有缓存
     */
    public function clearCache(): void
    {
        $locales = $this->getSupportedLocales();
        
        foreach ($locales as $locale) {
            Cache::forget("pattern_content_{$this->id}_{$locale}");
            Cache::forget("pattern_headings_{$this->id}_{$locale}");
        }
    }
}
