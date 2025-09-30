<?php

namespace App\Http\Controllers;

use App\Models\PatternCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PatternCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = PatternCategory::orderBy('sort_order')->get();
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        $category = PatternCategory::create($validated);
        
        return redirect()->route('categories.index')
            ->with('success', '分类创建成功！');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $category = PatternCategory::where('slug', $slug)->firstOrFail();
        $patterns = $category->designPatterns()
            ->where('is_published', true)
            ->orderBy('sort_order')
            ->paginate(10);
            
        return view('categories.show', compact('category', 'patterns'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PatternCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PatternCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'integer',
        ]);
        
        // 如果名称改变，更新slug
        if ($category->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        $category->update($validated);
        
        return redirect()->route('categories.index')
            ->with('success', '分类更新成功！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatternCategory $category)
    {
        // 检查是否有关联的设计模式
        if ($category->designPatterns()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', '无法删除此分类，因为它包含设计模式！');
        }
        
        $category->delete();
        
        return redirect()->route('categories.index')
            ->with('success', '分类删除成功！');
    }
}
