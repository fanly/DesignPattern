# 迭代器模式 (Iterator Pattern)

## 概述

迭代器模式提供一种方法顺序访问一个聚合对象中各个元素，而又不需暴露该对象的内部表示。它将遍历逻辑与聚合对象分离，使得聚合对象可以专注于数据存储，而迭代器专注于遍历。

## 设计意图

- **遍历封装**：将遍历逻辑封装在迭代器中
- **统一接口**：为不同的聚合结构提供统一的遍历接口
- **内部表示隐藏**：不暴露聚合对象的内部结构
- **多遍历支持**：支持多种遍历方式

## Laravel 中的实现

### 1. Collection 迭代器

Laravel 的 Collection 类是迭代器模式的典型实现：

```php
// Illuminate\Support\Collection.php
class Collection implements ArrayAccess, Arrayable, Countable, IteratorAggregate, Jsonable, JsonSerializable
{
    protected $items = [];
    
    // 实现 IteratorAggregate 接口
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
    
    // 多种遍历方法
    public function each(callable $callback)
    {
        foreach ($this->items as $key => $item) {
            if ($callback($item, $key) === false) {
                break;
            }
        }
        
        return $this;
    }
    
    public function map(callable $callback)
    {
        $keys = array_keys($this->items);
        $items = array_map($callback, $this->items, $keys);
        
        return new static(array_combine($keys, $items));
    }
    
    public function filter(callable $callback = null)
    {
        if ($callback) {
            return new static(array_filter($this->items, $callback, ARRAY_FILTER_USE_BOTH));
        }
        
        return new static(array_filter($this->items));
    }
}

// 使用示例
$collection = collect([1, 2, 3, 4, 5]);

// 使用迭代器遍历
foreach ($collection as $item) {
    echo $item;
}

// 使用高阶函数遍历
$collection->each(function ($item) {
    echo $item;
});
```

### 2. 查询结果迭代器

Eloquent 查询结果也实现了迭代器模式：

```php
// Illuminate\Database\Eloquent\Collection.php
class Collection extends BaseCollection
{
    // 重写迭代器方法，支持模型序列化
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }
    
    // 分块处理大数据集
    public function chunk($size)
    {
        if ($size <= 0) {
            return new static;
        }
        
        $chunks = [];
        
        foreach (array_chunk($this->items, $size, true) as $chunk) {
            $chunks[] = new static($chunk);
        }
        
        return new static($chunks);
    }
    
    // 延迟加载关联关系
    public function load($relations)
    {
        if (count($this->items) > 0) {
            if (is_string($relations)) {
                $relations = func_get_args();
            }
            
            $query = $this->first()->newQueryWithoutRelationships()->with($relations);
            
            $this->items = $query->eagerLoadRelations($this->items);
        }
        
        return $this;
    }
}

// 使用示例
$users = User::where('active', true)->get();

// 使用迭代器遍历
foreach ($users as $user) {
    echo $user->name;
}

// 分块处理大数据
User::chunk(200, function ($users) {
    foreach ($users as $user) {
        // 处理用户
    }
});
```

### 3. 文件系统迭代器

Laravel 的文件系统也使用了迭代器模式：

```php
// Illuminate\Filesystem\Filesystem.php
class Filesystem
{
    // 遍历目录
    public function files($directory, $hidden = false)
    {
        return iterator_to_array(
            $this->getFiles($directory, $hidden), false
        );
    }
    
    protected function getFiles($path, $hidden)
    {
        return new FilterIterator(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS)
            ), function ($file) use ($hidden) {
                return $hidden || !$file->isDot() && substr($file->getFilename(), 0, 1) !== '.';
            }
        );
    }
    
    // 递归遍历目录
    public function allFiles($directory, $hidden = false)
    {
        return iterator_to_array(
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS)
            ), false
        );
    }
}

// 使用示例
$files = Storage::files('documents');
$allFiles = Storage::allFiles('documents');

foreach ($files as $file) {
    echo $file;
}
```

## 实际应用场景

### 1. 分页迭代器

实现支持分页的迭代器：

```php
class PaginatedIterator implements Iterator
{
    protected $currentPage = 1;
    protected $perPage = 10;
    protected $currentItems = [];
    protected $total = 0;
    protected $query;
    
    public function __construct($query, $perPage = 10)
    {
        $this->query = $query;
        $this->perPage = $perPage;
        $this->loadPage(1);
    }
    
    public function current()
    {
        return current($this->currentItems);
    }
    
    public function key()
    {
        return key($this->currentItems);
    }
    
    public function next()
    {
        next($this->currentItems);
        
        // 如果当前页遍历完毕，加载下一页
        if (!current($this->currentItems) && $this->hasMorePages()) {
            $this->loadPage($this->currentPage + 1);
            reset($this->currentItems);
        }
    }
    
    public function rewind()
    {
        $this->loadPage(1);
        reset($this->currentItems);
    }
    
    public function valid()
    {
        return current($this->currentItems) !== false;
    }
    
    protected function loadPage($page)
    {
        $this->currentPage = $page;
        $this->currentItems = $this->query->forPage($page, $this->perPage)->get()->all();
    }
    
    protected function hasMorePages()
    {
        return count($this->currentItems) === $this->perPage;
    }
}

// 使用示例
$iterator = new PaginatedIterator(User::query(), 100);

foreach ($iterator as $user) {
    // 自动处理分页
    processUser($user);
}
```

### 2. 过滤迭代器

实现支持过滤的迭代器：

```php
class FilterIterator implements Iterator
{
    protected $innerIterator;
    protected $filterCallback;
    protected $current;
    protected $key;
    
    public function __construct(Iterator $innerIterator, callable $filterCallback)
    {
        $this->innerIterator = $innerIterator;
        $this->filterCallback = $filterCallback;
        $this->rewind();
    }
    
    public function current()
    {
        return $this->current;
    }
    
    public function key()
    {
        return $this->key;
    }
    
    public function next()
    {
        $this->innerIterator->next();
        $this->findNextValid();
    }
    
    public function rewind()
    {
        $this->innerIterator->rewind();
        $this->findNextValid();
    }
    
    public function valid()
    {
        return $this->current !== null;
    }
    
    protected function findNextValid()
    {
        $this->current = null;
        $this->key = null;
        
        while ($this->innerIterator->valid()) {
            $current = $this->innerIterator->current();
            $key = $this->innerIterator->key();
            
            if (call_user_func($this->filterCallback, $current, $key)) {
                $this->current = $current;
                $this->key = $key;
                break;
            }
            
            $this->innerIterator->next();
        }
    }
}

// 使用示例
$numbers = new ArrayIterator([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);
$evenIterator = new FilterIterator($numbers, function ($number) {
    return $number % 2 === 0;
});

foreach ($evenIterator as $evenNumber) {
    echo $evenNumber; // 输出: 2, 4, 6, 8, 10
}
```

### 3. 转换迭代器

实现支持数据转换的迭代器：

```php
class MapIterator implements Iterator
{
    protected $innerIterator;
    protected $mapCallback;
    
    public function __construct(Iterator $innerIterator, callable $mapCallback)
    {
        $this->innerIterator = $innerIterator;
        $this->mapCallback = $mapCallback;
    }
    
    public function current()
    {
        return call_user_func($this->mapCallback, $this->innerIterator->current(), $this->innerIterator->key());
    }
    
    public function key()
    {
        return $this->innerIterator->key();
    }
    
    public function next()
    {
        $this->innerIterator->next();
    }
    
    public function rewind()
    {
        $this->innerIterator->rewind();
    }
    
    public function valid()
    {
        return $this->innerIterator->valid();
    }
}

// 使用示例
$users = new ArrayIterator([
    ['name' => 'John', 'age' => 25],
    ['name' => 'Jane', 'age' => 30]
]);

$nameIterator = new MapIterator($users, function ($user) {
    return $user['name'];
});

foreach ($nameIterator as $name) {
    echo $name; // 输出: John, Jane
}
```

## 源码分析要点

### 1. 迭代器接口实现

Laravel 中的迭代器模式遵循 PHP 的迭代器接口：

```php
// 基本的迭代器接口
interface Iterator extends Traversable
{
    public function current();
    public function key();
    public function next();
    public function rewind();
    public function valid();
}

// 聚合器接口
interface IteratorAggregate extends Traversable
{
    public function getIterator();
}
```

### 2. 延迟加载迭代器

Laravel 使用延迟加载优化迭代性能：

```php
class LazyCollection implements IteratorAggregate
{
    protected $source;
    
    public function __construct($source)
    {
        $this->source = $source;
    }
    
    public function getIterator()
    {
        // 延迟生成迭代器
        if (is_callable($this->source)) {
            $result = ($this->source)();
            return $result instanceof Iterator ? $result : new ArrayIterator((array) $result);
        }
        
        return new ArrayIterator((array) $this->source);
    }
    
    public function each(callable $callback)
    {
        foreach ($this as $key => $value) {
            if ($callback($value, $key) === false) {
                break;
            }
        }
        
        return $this;
    }
}
```

### 3. 链式迭代器操作

Laravel 支持链式迭代器操作：

```php
$result = collect([1, 2, 3, 4, 5])
    ->filter(fn($n) => $n % 2 === 0)  // 过滤偶数
    ->map(fn($n) => $n * 2)           // 乘以2
    ->take(2)                         // 取前2个
    ->values();                       // 重新索引
    
// 结果: [4, 8]
```

## 最佳实践

### 1. 合理使用迭代器模式

**适用场景：**
- 需要统一遍历不同结构的数据
- 需要隐藏聚合对象的内部结构
- 需要支持多种遍历方式
- 需要延迟加载或分批处理数据

**不适用场景：**
- 数据结构简单，直接遍历即可
- 性能要求极高，需要直接访问内部结构

### 2. Laravel 中的迭代器实践

**使用 Collection 进行数据处理：**
```php
// 复杂的数据处理链
$activeUsers = User::all()
    ->filter(fn($user) => $user->isActive())
    ->map(fn($user) => [
        'name' => $user->name,
        'email' => $user->email,
        'last_login' => $user->last_login?->diffForHumans()
    ])
    ->sortBy('name')
    ->values();

// 使用高阶方法
$total = collect([1, 2, 3, 4, 5])->sum();
$average = collect([1, 2, 3, 4, 5])->avg();
$max = collect([1, 2, 3, 4, 5])->max();
```

**处理大数据集：**
```php
// 使用 chunk 处理大数据
User::chunk(1000, function ($users) {
    foreach ($users as $user) {
        // 处理用户，避免内存溢出
    }
});

// 使用 cursor 进行流式处理
foreach (User::cursor() as $user) {
    // 流式处理，内存友好
}
```

**自定义迭代器：**
```php
class DatabaseIterator implements Iterator
{
    protected $query;
    protected $current;
    protected $key = 0;
    
    public function __construct($query)
    {
        $this->query = $query;
    }
    
    public function current()
    {
        return $this->current;
    }
    
    public function key()
    {
        return $this->key;
    }
    
    public function next()
    {
        $this->key++;
        $this->current = $this->query->next();
    }
    
    public function rewind()
    {
        $this->key = 0;
        $this->current = $this->query->first();
    }
    
    public function valid()
    {
        return $this->current !== null;
    }
}
```

### 3. 测试迭代器模式

**测试迭代器功能：**
```php
public function test_iterator_traversal()
{
    $data = [1, 2, 3, 4, 5];
    $iterator = new ArrayIterator($data);
    $result = [];
    
    foreach ($iterator as $item) {
        $result[] = $item;
    }
    
    $this->assertEquals($data, $result);
}

public function test_filter_iterator()
{
    $data = [1, 2, 3, 4, 5];
    $iterator = new FilterIterator(
        new ArrayIterator($data),
        fn($n) => $n % 2 === 0
    );
    
    $result = iterator_to_array($iterator);
    $this->assertEquals([2, 4], $result);
}
```

**测试性能优化：**
```php
public function test_lazy_collection_performance()
{
    // 测试延迟加载的性能优势
    $start = microtime(true);
    
    $collection = LazyCollection::make(function () {
        for ($i = 0; $i < 1000000; $i++) {
            yield $i;
        }
    });
    
    $result = $collection->take(10)->all();
    $end = microtime(true);
    
    $this->assertLessThan(1, $end - $start); // 应该在1秒内完成
    $this->assertCount(10, $result);
}
```

## 与其他模式的关系

### 1. 与组合模式

迭代器模式常与组合模式结合遍历复杂结构：

```php
class CompositeIterator implements Iterator
{
    protected $stack = [];
    
    public function __construct(Iterator $iterator)
    {
        $this->stack[] = $iterator;
    }
    
    public function current()
    {
        if (empty($this->stack)) {
            return null;
        }
        
        $iterator = end($this->stack);
        return $iterator->current();
    }
    
    public function next()
    {
        $iterator = end($this->stack);
        $current = $iterator->current();
        
        if ($current instanceof Composite) {
            $this->stack[] = $current->getIterator();
        } else {
            $iterator->next();
        }
    }
}
```

### 2. 与访问者模式

迭代器模式可以与访问者模式结合：

```php
class Traverser
{
    public function traverse(Iterator $iterator, Visitor $visitor)
    {
        foreach ($iterator as $element) {
            $element->accept($visitor);
        }
    }
}
```

### 3. 与工厂模式

迭代器模式使用工厂模式创建特定迭代器：

```php
class IteratorFactory
{
    public function createIterator($collection, $type = 'default')
    {
        switch ($type) {
            case 'filtered':
                return new FilterIterator($collection->getIterator(), fn($item) => $item->isValid());
            case 'sorted':
                return new SortedIterator($collection->getIterator());
            default:
                return $collection->getIterator();
        }
    }
}
```

## 性能考虑

### 1. 内存使用优化

对于大数据集，使用生成器避免内存溢出：

```php
class GeneratorIterator implements IteratorAggregate
{
    protected $generator;
    
    public function __construct(callable $generator)
    {
        $this->generator = $generator;
    }
    
    public function getIterator()
    {
        $result = ($this->generator)();
        
        if ($result instanceof Generator) {
            return $result;
        }
        
        return new ArrayIterator((array) $result);
    }
}

// 使用生成器处理大数据
$largeDataset = new GeneratorIterator(function () {
    for ($i = 0; $i < 1000000; $i++) {
        yield $i;
    }
});

foreach ($largeDataset as $item) {
    // 处理每个项目，内存友好
}
```

### 2. 缓存策略

对于昂贵的迭代操作，可以使用缓存：

```php
class CachedIterator implements Iterator
{
    protected $innerIterator;
    protected $cache = [];
    protected $cached = false;
    
    public function __construct(Iterator $innerIterator)
    {
        $this->innerIterator = $innerIterator;
    }
    
    public function current()
    {
        if (!$this->cached) {
            $this->cacheData();
        }
        
        return current($this->cache);
    }
    
    protected function cacheData()
    {
        $this->cache = iterator_to_array($this->innerIterator);
        $this->cached = true;
        reset($this->cache);
    }
}
```

## 总结

迭代器模式在 Laravel 框架中有着广泛的应用，特别是在 Collection 类、Eloquent 查询结果和文件系统中。它通过将遍历逻辑封装在迭代器中，实现了数据访问的统一接口和内部表示的隐藏。

迭代器模式的优势在于：
- **统一接口**：为不同数据结构提供一致的遍历方式
- **封装性**：隐藏聚合对象的内部结构
- **灵活性**：支持多种遍历算法和过滤条件
- **性能优化**：支持延迟加载和分批处理

在 Laravel 开发中，合理使用迭代器模式可以创建出高效、灵活的数据处理系统，特别是在需要处理复杂数据结构和大型数据集的场景中。