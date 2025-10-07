<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\PatternCategory;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Home extends Component
{   
    public function render()
    {
        $cacheKey = 'home_categories_' . app()->getLocale();
        
        $categories = cache()->remember($cacheKey, 900, function () { // 15分钟
            return PatternCategory::with(['designPatterns' => function($query) {
                $query->where('is_published', true);
            }])
                ->orderBy(app()->getLocale() === 'zh' ? 'name_zh' : 'name_en')
                ->get();
        });
        
        return view('livewire.pages.home', [
            'categories' => $categories
        ]);
    }
    
    public function confirmDelete($id)
    {
        $this->dialog()->confirm([
            'title'       => '确认删除',
            'description' => '确定要删除这个分类吗？所有关联的设计模式也将被删除',
            'icon'        => 'error',
            'accept'      => [
                'label'  => '确认删除',
                'method' => 'deleteCategory',
                'params' => $id
            ],
            'reject' => [
                'label' => '取消'
            ]
        ]);
    }
    
    public function deleteCategory($id)
    {
        // 删除逻辑
    }
}