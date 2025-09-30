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
        return view('livewire.pages.home', [
            'categories' => PatternCategory::with('designPatterns')
                ->orderBy(app()->getLocale() === 'zh' ? 'name_zh' : 'name_en')
                ->get()
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