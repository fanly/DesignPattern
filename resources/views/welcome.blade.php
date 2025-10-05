<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Laravel设计模式电子书 - 通过Laravel源码深入理解设计模式">
    <meta name="keywords" content="Laravel,设计模式,软件架构,编程,开发">
    
    <title>@lang('site.title')</title>

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles -->
    <style>
        /*! tailwindcss v4.0.14 | MIT License | https://tailwindcss.com */
        @layer theme, base, components, utilities;
        
        @layer theme {
            :root, :host {
                --font-sans: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                --font-mono: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                --color-slate-50: #f8fafc;
                --color-slate-100: #f1f5f9;
                --color-slate-200: #e2e8f0;
                --color-slate-300: #cbd5e1;
                --color-slate-400: #94a3b8;
                --color-slate-500: #64748b;
                --color-slate-600: #475569;
                --color-slate-700: #334155;
                --color-slate-800: #1e293b;
                --color-slate-900: #0f172a;
                --color-slate-950: #020617;
                --color-emerald-500: #10b981;
                --color-emerald-600: #059669;
                --color-emerald-700: #047857;
                --color-blue-500: #3b82f6;
                --color-blue-600: #2563eb;
                --color-blue-700: #1d4ed8;
                --color-amber-500: #f59e0b;
                --color-amber-600: #d97706;
                --color-violet-500: #8b5cf6;
                --color-violet-600: #7c3aed;
                --color-rose-500: #f43f5e;
                --color-rose-600: #e11d48;
                --color-black: #000;
                --color-white: #fff;
                --spacing: 0.25rem;
                --text-sm: 0.875rem;
                --text-base: 1rem;
                --text-lg: 1.125rem;
                --text-xl: 1.25rem;
                --text-2xl: 1.5rem;
                --text-3xl: 1.875rem;
                --text-4xl: 2.25rem;
                --text-5xl: 3rem;
                --font-weight-normal: 400;
                --font-weight-medium: 500;
                --font-weight-semibold: 600;
                --font-weight-bold: 700;
                --leading-tight: 1.25;
                --leading-normal: 1.5;
                --leading-relaxed: 1.625;
                --radius-sm: 0.25rem;
                --radius-md: 0.375rem;
                --radius-lg: 0.5rem;
                --radius-xl: 0.75rem;
                --radius-2xl: 1rem;
                --default-transition-duration: 0.15s;
                --default-transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            }
        }

        @layer base {
            *, ::before, ::after {
                box-sizing: border-box;
                border-width: 0;
                border-style: solid;
                border-color: currentColor;
            }
            
            html {
                line-height: 1.5;
                -webkit-text-size-adjust: 100%;
                font-family: var(--font-sans);
                -webkit-tap-highlight-color: transparent;
            }
            
            body {
                margin: 0;
                line-height: inherit;
                background-color: var(--color-slate-50);
                color: var(--color-slate-900);
            }
            
            h1, h2, h3, h4, h5, h6 {
                font-size: inherit;
                font-weight: inherit;
            }
            
            a {
                color: inherit;
                text-decoration: inherit;
            }
        }

        @layer utilities {
            .container {
                width: 100%;
                margin-left: auto;
                margin-right: auto;
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            @media (min-width: 640px) {
                .container {
                    max-width: 640px;
                }
            }
            
            @media (min-width: 768px) {
                .container {
                    max-width: 768px;
                }
            }
            
            @media (min-width: 1024px) {
                .container {
                    max-width: 1024px;
                }
            }
            
            @media (min-width: 1280px) {
                .container {
                    max-width: 1280px;
                }
            }
            
            .flex { display: flex }
            .grid { display: grid }
            .hidden { display: none }
            .block { display: block }
            .inline-flex { display: inline-flex }
            .items-center { align-items: center }
            .justify-between { justify-content: space-between }
            .justify-center { justify-content: center }
            .text-center { text-align: center }
            .font-semibold { font-weight: var(--font-weight-semibold) }
            .font-bold { font-weight: var(--font-weight-bold) }
            .text-2xl { font-size: var(--text-2xl) }
            .text-3xl { font-size: var(--text-3xl) }
            .text-4xl { font-size: var(--text-4xl) }
            .text-5xl { font-size: var(--text-5xl) }
            .leading-tight { line-height: var(--leading-tight) }
            .leading-normal { line-height: var(--leading-normal) }
            .bg-white { background-color: var(--color-white) }
            .bg-slate-50 { background-color: var(--color-slate-50) }
            .bg-slate-100 { background-color: var(--color-slate-100) }
            .bg-emerald-500 { background-color: var(--color-emerald-500) }
            .bg-blue-500 { background-color: var(--color-blue-500) }
            .bg-amber-500 { background-color: var(--color-amber-500) }
            .bg-violet-500 { background-color: var(--color-violet-500) }
            .bg-rose-500 { background-color: var(--color-rose-500) }
            .text-white { color: var(--color-white) }
            .text-slate-600 { color: var(--color-slate-600) }
            .text-slate-700 { color: var(--color-slate-700) }
            .text-slate-800 { color: var(--color-slate-800) }
            .text-slate-900 { color: var(--color-slate-900) }
            .rounded-lg { border-radius: var(--radius-lg) }
            .rounded-xl { border-radius: var(--radius-xl) }
            .rounded-2xl { border-radius: var(--radius-2xl) }
            .shadow-sm { box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) }
            .shadow-md { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) }
            .shadow-lg { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1) }
            .p-4 { padding: calc(var(--spacing) * 4) }
            .p-6 { padding: calc(var(--spacing) * 6) }
            .p-8 { padding: calc(var(--spacing) * 8) }
            .px-4 { padding-left: calc(var(--spacing) * 4); padding-right: calc(var(--spacing) * 4) }
            .px-6 { padding-left: calc(var(--spacing) * 6); padding-right: calc(var(--spacing) * 6) }
            .py-8 { padding-top: calc(var(--spacing) * 8); padding-bottom: calc(var(--spacing) * 8) }
            .py-12 { padding-top: calc(var(--spacing) * 12); padding-bottom: calc(var(--spacing) * 12) }
            .py-16 { padding-top: calc(var(--spacing) * 16); padding-bottom: calc(var(--spacing) * 16) }
            .mb-4 { margin-bottom: calc(var(--spacing) * 4) }
            .mb-6 { margin-bottom: calc(var(--spacing) * 6) }
            .mb-8 { margin-bottom: calc(var(--spacing) * 8) }
            .mt-4 { margin-top: calc(var(--spacing) * 4) }
            .mt-8 { margin-top: calc(var(--spacing) * 8) }
            .gap-4 { gap: calc(var(--spacing) * 4) }
            .gap-6 { gap: calc(var(--spacing) * 6) }
            .gap-8 { gap: calc(var(--spacing) * 8) }
            .transition-all { transition-property: all; transition-timing-function: var(--default-transition-timing-function); transition-duration: var(--default-transition-duration) }
            .hover\:scale-105:hover { transform: scale(1.05) }
            .hover\:shadow-xl:hover { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) }
            
            @media (min-width: 768px) {
                .md\:text-5xl { font-size: var(--text-5xl) }
                .md\:text-6xl { font-size: 3.75rem }
                .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)) }
                .md\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)) }
                .md\:gap-8 { gap: calc(var(--spacing) * 8) }
            }
            
            @media (min-width: 1024px) {
                .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)) }
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-slate-200">
        <div class="container">
            <div class="flex items-center justify-between py-4">
                <div class="flex items-center gap-6">
                    <h1 class="text-2xl font-bold text-slate-900">@lang('site.title')</h1>
                    <nav class="hidden md:flex items-center gap-6">
                        <a href="#patterns" class="text-slate-700 hover:text-slate-900 transition-colors">
                            @if(app()->getLocale() === 'zh')设计模式@elseDesign Patterns@endif
                        </a>
                        <a href="#about" class="text-slate-700 hover:text-slate-900 transition-colors">
                            @if(app()->getLocale() === 'zh')关于@elseAbout@endif
                        </a>
                        <a href="#contact" class="text-slate-700 hover:text-slate-900 transition-colors">
                            @if(app()->getLocale() === 'zh')联系@elseContact@endif
                        </a>
                    </nav>
                </div>
                
                <!-- Language Switcher -->
                <livewire:language-switcher />
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="py-16 bg-white">
        <div class="container">
            <div class="text-center max-w-4xl mx-auto">
                <h1 class="text-4xl md:text-5xl font-bold text-slate-900 leading-tight mb-6">
                    @if(app()->getLocale() === 'zh')
                    通过Laravel源码深入理解设计模式
                    @else
                    Deep Understanding of Design Patterns through Laravel Source Code
                    @endif
                </h1>
                <p class="text-xl text-slate-600 leading-relaxed mb-8">
                    @if(app()->getLocale() === 'zh')
                    本电子书通过分析Laravel框架的实际源码，系统讲解23种经典设计模式的应用场景和实现原理。
                    每个模式都配有详细的代码示例和实际应用案例。
                    @else
                    This ebook systematically explains 23 classic design patterns through analysis of Laravel framework's actual source code.
                    Each pattern comes with detailed code examples and practical application cases.
                    @endif
                </p>
                <div class="flex gap-4 justify-center">
                    <a href="#patterns" class="bg-emerald-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-emerald-600 transition-colors">
                        @if(app()->getLocale() === 'zh')开始学习@elseStart Learning@endif
                    </a>
                    <a href="#about" class="border border-slate-300 text-slate-700 px-8 py-3 rounded-lg font-semibold hover:bg-slate-50 transition-colors">
                        @if(app()->getLocale() === 'zh')了解更多@elseLearn More@endif
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Design Patterns Grid -->
    <section id="patterns" class="py-16 bg-slate-50">
        <div class="container">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">
                    @if(app()->getLocale() === 'zh')设计模式分类@elseDesign Pattern Categories@endif
                </h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                    @if(app()->getLocale() === 'zh')
                    按照创建型、结构型、行为型三大类别系统学习设计模式
                    @else
                    Systematically learn design patterns through three major categories: Creational, Structural, and Behavioral
                    @endif
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- 创建型模式 -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">
                        @if(app()->getLocale() === 'zh')创建型模式@elseCreational Patterns@endif
                    </h3>
                    <p class="text-slate-600 mb-4">
                        @if(app()->getLocale() === 'zh')
                        关注对象创建机制，提高系统灵活性
                        @else
                        Focus on object creation mechanisms to improve system flexibility
                        @endif
                    </p>
                    <ul class="space-y-2 text-slate-700">
                        @if(app()->getLocale() === 'zh')
                        <li>• 工厂方法模式</li>
                        <li>• 抽象工厂模式</li>
                        <li>• 建造者模式</li>
                        <li>• 原型模式</li>
                        <li>• 单例模式</li>
                        @else
                        <li>• Factory Method</li>
                        <li>• Abstract Factory</li>
                        <li>• Builder</li>
                        <li>• Prototype</li>
                        <li>• Singleton</li>
                        @endif
                    </ul>
                </div>

                <!-- 结构型模式 -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 bg-amber-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">
                        @if(app()->getLocale() === 'zh')结构型模式@elseStructural Patterns@endif
                    </h3>
                    <p class="text-slate-600 mb-4">
                        @if(app()->getLocale() === 'zh')
                        处理类或对象的组合，形成更大结构
                        @else
                        Handle class or object composition to form larger structures
                        @endif
                    </p>
                    <ul class="space-y-2 text-slate-700">
                        @if(app()->getLocale() === 'zh')
                        <li>• 适配器模式</li>
                        <li>• 桥接模式</li>
                        <li>• 组合模式</li>
                        <li>• 装饰器模式</li>
                        <li>• 外观模式</li>
                        <li>• 享元模式</li>
                        <li>• 代理模式</li>
                        @else
                        <li>• Adapter</li>
                        <li>• Bridge</li>
                        <li>• Composite</li>
                        <li>• Decorator</li>
                        <li>• Facade</li>
                        <li>• Flyweight</li>
                        <li>• Proxy</li>
                        @endif
                    </ul>
                </div>

                <!-- 行为型模式 -->
                <div class="bg-white rounded-xl shadow-lg p-8 hover:shadow-xl transition-all">
                    <div class="w-12 h-12 bg-violet-500 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">
                        @if(app()->getLocale() === 'zh')行为型模式@elseBehavioral Patterns@endif
                    </h3>
                    <p class="text-slate-600 mb-4">
                        @if(app()->getLocale() === 'zh')
                        关注对象间的通信和职责分配
                        @else
                        Focus on communication and responsibility distribution between objects
                        @endif
                    </p>
                    <ul class="space-y-2 text-slate-700">
                        @if(app()->getLocale() === 'zh')
                        <li>• 责任链模式</li>
                        <li>• 命令模式</li>
                        <li>• 迭代器模式</li>
                        <li>• 中介者模式</li>
                        <li>• 备忘录模式</li>
                        <li>• 观察者模式</li>
                        <li>• 状态模式</li>
                        <li>• 策略模式</li>
                        <li>• 模板方法模式</li>
                        <li>• 访问者模式</li>
                        @else
                        <li>• Chain of Responsibility</li>
                        <li>• Command</li>
                        <li>• Iterator</li>
                        <li>• Mediator</li>
                        <li>• Memento</li>
                        <li>• Observer</li>
                        <li>• State</li>
                        <li>• Strategy</li>
                        <li>• Template Method</li>
                        <li>• Visitor</li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="container">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">
                    @if(app()->getLocale() === 'zh')特色功能@elseFeatures@endif
                </h2>
                <p class="text-lg text-slate-600">
                    @if(app()->getLocale() === 'zh')
                    专为Laravel开发者设计的学习体验
                    @else
                    Learning experience designed specifically for Laravel developers
                    @endif
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        @if(app()->getLocale() === 'zh')源码分析@elseSource Code Analysis@endif
                    </h3>
                    <p class="text-slate-600">
                        @if(app()->getLocale() === 'zh')
                        基于Laravel实际源码，学习真实应用场景
                        @else
                        Learn real application scenarios based on Laravel's actual source code
                        @endif
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        @if(app()->getLocale() === 'zh')Markdown格式@elseMarkdown Format@endif
                    </h3>
                    <p class="text-slate-600">
                        @if(app()->getLocale() === 'zh')
                        使用Markdown编写，支持代码高亮和数学公式
                        @else
                        Written in Markdown, supporting code highlighting and mathematical formulas
                        @endif
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        @if(app()->getLocale() === 'zh')实时预览@elseReal-time Preview@endif
                    </h3>
                    <p class="text-slate-600">
                        @if(app()->getLocale() === 'zh')
                        边写边看，实时渲染Markdown内容
                        @else
                        Write and preview simultaneously with real-time Markdown rendering
                        @endif
                    </p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-violet-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900 mb-2">
                        @if(app()->getLocale() === 'zh')多语言支持@elseMultilingual Support@endif
                    </h3>
                    <p class="text-slate-600">
                        @if(app()->getLocale() === 'zh')
                        支持中英文切换，满足不同语言需求
                        @else
                        Support for Chinese and English switching to meet different language needs
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-white py-12 mt-16">
        <div class="container">
            <div class="grid md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-semibold mb-4">@lang('site.title')</h3>
                    <p class="text-slate-400">
                        @if(app()->getLocale() === 'zh')
                        通过Laravel源码深入理解设计模式的电子书项目
                        @else
                        An ebook project for deep understanding of design patterns through Laravel source code
                        @endif
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">
                        @if(app()->getLocale() === 'zh')快速链接@elseQuick Links@endif
                    </h3>
                    <ul class="space-y-2 text-slate-400">
                        <li><a href="#patterns" class="hover:text-white transition-colors">
                            @if(app()->getLocale() === 'zh')设计模式@elseDesign Patterns@endif
                        </a></li>
                        <li><a href="#about" class="hover:text-white transition-colors">
                            @if(app()->getLocale() === 'zh')关于项目@elseAbout@endif
                        </a></li>
                        <li><a href="#contact" class="hover:text-white transition-colors">
                            @if(app()->getLocale() === 'zh')联系我们@elseContact@endif
                        </a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-4">
                        @if(app()->getLocale() === 'zh')广告合作@elseAdvertising@endif
                    </h3>
                    <div class="bg-slate-800 rounded-lg p-4 text-center">
                        <p class="text-sm text-slate-300 mb-2">
                            @if(app()->getLocale() === 'zh')技术广告位@elseTech Ad Space@endif
                        </p>
                        <div class="bg-slate-700 rounded px-3 py-2 text-xs">
                            @if(app()->getLocale() === 'zh')联系邮箱@elseContact Email@endif: contact@laravel-dp.com
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-800 mt-8 pt-8 text-center text-slate-400">
                <p>&copy; 2024 @lang('site.title'). 
                    @if(app()->getLocale() === 'zh')
                    保留所有权利.
                    @else
                    All rights reserved.
                    @endif
                </p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for interactivity -->
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>