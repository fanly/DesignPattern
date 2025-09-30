<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\LaravelMarkdown\MarkdownRenderer;
use Illuminate\Support\Facades\Storage;

class DesignPattern extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'file_path',
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
     * 获取设计模式的Markdown内容
     */
    public function getContent(): string
    {
        if (!$this->file_path || !Storage::exists($this->file_path)) {
            return "# {$this->name}\n\n内容正在编写中...";
        }
        
        return Storage::get($this->file_path);
    }
    
    /**
     * 获取渲染后的HTML内容
     */
    public function getHtmlContent(): string
    {
        $markdown = $this->getContent();
        
        // 为标题添加ID属性
        $markdown = preg_replace_callback('/^(#{1,6})\s+(.+)$/m', function($matches) {
            $level = strlen($matches[1]);
            $title = $matches[2];
            $slug = strtolower(preg_replace('/[^a-zA-Z0-9\x{4e00}-\x{9fa5}]/u', '-', $title));
            $slug = preg_replace('/-+/', '-', $slug);
            $slug = trim($slug, '-');
            return str_repeat('#', $level) . ' ' . $title . "\n\n" . '<span id="' . $slug . '"></span>';
        }, $markdown);
        
        $html = app(MarkdownRenderer::class)->toHtml($markdown);
        
        // 返回原始HTML，样式由外层容器控制
        return $html;
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
    public function saveContent(string $content): bool
    {
        if (!$this->file_path) {
            $this->file_path = "patterns/{$this->slug}.md";
            $this->save();
        }
        
        return Storage::put($this->file_path, $content);
    }
}
