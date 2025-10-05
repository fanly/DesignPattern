<?php

namespace App\Livewire;

use App\Services\MarkdownService;
use Livewire\Component;

class MarkdownDemo extends Component
{
    public string $content = '';
    public string $parsedContent = '';

    protected $markdownService;

    public function boot(MarkdownService $markdownService)
    {
        $this->markdownService = $markdownService;
    }

    public function mount()
    {
        $this->content = $this->getDefaultContent();
        $this->parseContent();
    }

    public function updatedContent()
    {
        $this->parseContent();
    }

    protected function parseContent()
    {
        $this->parsedContent = $this->markdownService->toHtml($this->content);
    }

    protected function getDefaultContent()
    {
        return <<<'MARKDOWN'
# Markdown 功能演示

## 代码高亮测试

### PHP 代码
```php
<?php

class StrategyPattern
{
    private $strategy;
    
    public function __construct(StrategyInterface $strategy)
    {
        $this->strategy = $strategy;
    }
    
    public function execute($data)
    {
        return $this->strategy->process($data);
    }
}
```

### JavaScript 代码
```javascript
class Observer {
    constructor() {
        this.observers = [];
    }
    
    subscribe(fn) {
        this.observers.push(fn);
    }
    
    notify(data) {
        this.observers.forEach(fn => fn(data));
    }
}
```

## Mermaid 流程图测试

### 简单流程图
```mermaid
graph TD
    A[开始] --> B{条件判断}
    B -->|是| C[执行操作A]
    B -->|否| D[执行操作B]
    C --> E[结束]
    D --> E
```

### 类图示例
```mermaid
classDiagram
    class Strategy {
        <<interface>>
        +execute()
    }
    
    class ConcreteStrategyA {
        +execute()
    }
    
    class ConcreteStrategyB {
        +execute()
    }
    
    class Context {
        -strategy: Strategy
        +setStrategy(Strategy)
        +executeStrategy()
    }
    
    Strategy <|-- ConcreteStrategyA
    Strategy <|-- ConcreteStrategyB
    Context --> Strategy
```

## 其他 Markdown 功能

### 表格
| 设计模式 | 类型 | 用途 |
|---------|------|------|
| 单例模式 | 创建型 | 确保类只有一个实例 |
| 工厂模式 | 创建型 | 创建对象而不指定具体类 |
| 观察者模式 | 行为型 | 定义对象间的一对多依赖关系 |

### 任务列表
- [x] 代码高亮功能
- [x] Mermaid 图表支持
- [ ] 数学公式支持
- [ ] 自定义容器

### 引用
> 这是一个重要的引用内容，用来展示 Markdown 的引用功能。

### 链接和强调
这里有一个 [链接示例](https://example.com)，还有 **粗体文本** 和 *斜体文本*。

`行内代码` 也是支持的。
MARKDOWN;
    }

    public function render()
    {
        return view('livewire.markdown-demo');
    }
}