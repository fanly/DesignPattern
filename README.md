<<<<<<< HEAD
# Laravel-Design-Patterns
共创基于 Laravel 源代码的设计模式
=======
# Laravel设计模式电子书

基于Laravel和Livewire构建的设计模式电子书网站，专注于通过Laravel源码讲解设计模式。

## 功能特性

- 📚 **设计模式分类**：按创建型、结构型、行为型分类展示
- 📖 **Markdown支持**：使用spatie/laravel-markdown插件解析内容
- 🎨 **现代化UI**：基于Tailwind CSS的响应式设计
- ⚡ **Livewire组件**：实时交互，无需页面刷新
- 📱 **移动端适配**：完美支持各种屏幕尺寸

## 技术栈

- **后端框架**：Laravel 11.x
- **前端框架**：Livewire 3.x
- **样式框架**：Tailwind CSS 4.x
- **Markdown解析**：spatie/laravel-markdown
- **数据库**：MySQL/SQLite

## 项目结构

```
app/
├── Livewire/
│   ├── Pages/
│   │   ├── Home.php          # 首页组件
│   │   ├── PatternIndex.php  # 设计模式列表
│   │   └── PatternShow.php   # 模式详情页
├── Models/
│   ├── PatternCategory.php   # 分类模型
│   └── DesignPattern.php     # 设计模式模型
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php     # 主布局文件
│   └── livewire/
│       └── pages/            # Livewire页面组件
```

## 快速开始

1. 安装依赖：
```bash
composer install
npm install
```

2. 配置环境：
```bash
cp .env.example .env
php artisan key:generate
```

3. 数据库迁移：
```bash
php artisan migrate --seed
```

4. 构建前端资源：
```bash
npm run build
```

5. 启动服务：
```bash
php artisan serve
```

## 页面功能

### 首页 (/)
- 展示所有设计模式分类
- 每个分类显示包含的模式数量
- 响应式网格布局

### 设计模式列表 (/patterns)
- 按分类展示所有设计模式
- 简洁的卡片式布局
- 快速导航到详情页

### 模式详情页 (/patterns/{slug})
- Markdown内容解析和渲染
- 自动生成的目录导航
- 代码语法高亮支持
- 响应式左右布局

## 数据模型

### PatternCategory (分类)
- name: 分类名称
- slug: URL标识

### DesignPattern (设计模式)
- name: 模式名称
- slug: URL标识
- category_id: 所属分类
- description: 简要描述
- content: Markdown内容

## 自定义配置

### Tailwind CSS配置
项目使用Tailwind CSS 4.x，配置文件位于 `tailwind.config.js`，已集成typography插件用于Markdown样式。

### Markdown解析
使用spatie/laravel-markdown插件，支持扩展Markdown语法和自定义渲染。

## 开发指南

### 添加新设计模式
1. 在数据库中创建新的DesignPattern记录
2. 使用Markdown格式编写内容
3. 内容会自动解析为HTML并应用样式

### 样式定制
- 修改 `resources/css/app.css` 文件
- 使用Tailwind工具类进行样式调整
- 确保响应式设计兼容性

## 部署说明

1. 配置生产环境变量
2. 运行数据库迁移
3. 构建前端资源
4. 配置Web服务器

## 许可证

MIT License
>>>>>>> 90360af (完成Laravel设计模式电子书网站开发)
