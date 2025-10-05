# 单例模式 (Singleton Pattern)

## 概述

单例模式确保一个类只有一个实例，并提供一个全局访问点。这是最常用的设计模式之一，特别适用于需要全局唯一实例的场景，如配置管理、数据库连接、日志记录器等。

## 架构图

### 单例模式类图
```mermaid
classDiagram
    class Singleton {
        -static instance: Singleton
        -Singleton()
        +static getInstance(): Singleton
        +operation(): void
    }
    
    class Client {
        +main(): void
    }
    
    Client --> Singleton : getInstance()
    
    note for Singleton "私有构造函数\n确保只能通过getInstance()创建实例"
```

### Laravel 容器单例实现
```mermaid
classDiagram
    class Container {
        -static instance: Container
        -bindings: array
        -instances: array
        +static getInstance(): Container
        +singleton(abstract, concrete): void
        +make(abstract): object
        -resolve(abstract): object
    }
    
    class ServiceProvider {
        +register(): void
        +boot(): void
    }
    
    class ConfigRepository {
        -items: array
        +get(key): mixed
        +set(key, value): void
    }
    
    Container --> Container : getInstance()
    ServiceProvider --> Container : singleton()
    Container --> ConfigRepository : 创建单例实例
    
    note for Container "Laravel服务容器\n管理所有单例服务"
```

### 单例模式时序图
```mermaid
sequenceDiagram
    participant Client1
    participant Client2
    participant Singleton
    
    Client1->>Singleton: getInstance()
    alt 首次调用
        Singleton->>Singleton: new Singleton()
        Singleton-->>Client1: 返回新实例
    end
    
    Client2->>Singleton: getInstance()
    alt 后续调用
        Singleton-->>Client2: 返回已存在实例
    end
    
    Note over Client1,Client2: 两个客户端获得相同实例
```

### Laravel 服务容器单例流程
```mermaid
flowchart TD
    A[应用启动] --> B[创建Container实例]
    B --> C[注册服务提供者]
    C --> D[调用register方法]
    D --> E{是否为单例服务?}
    E -->|是| F[调用singleton方法]
    E -->|否| G[调用bind方法]
    F --> H[标记为共享实例]
    G --> I[标记为非共享]
    H --> J[首次解析时创建实例]
    I --> K[每次解析都创建新实例]
    J --> L[存储在instances数组]
    L --> M[后续请求返回缓存实例]
    K --> N[每次返回新实例]
    
    style F fill:#e1f5fe
    style H fill:#e8f5e8
    style L fill:#fff3e0
```

## 设计意图

- **唯一性**：确保类只有一个实例存在
- **全局访问**：提供统一的访问入口
- **资源控制**：避免重复创建对象，节省系统资源
- **状态共享**：便于在系统各处共享状态信息

## Laravel 中的实现

### 1. 容器级别的单例模式

Laravel 的服务容器本身就是单例模式的典型实现。容器通过静态变量 `$instance` 来维护全局唯一的实例：

```php
// Illuminate\Container\Container.php
protected static $instance;

public static function getInstance()
{
    return static::$instance ??= new static;
}
```

### 2. 服务绑定中的单例注册

在服务提供者中，可以使用 `singleton` 方法注册单例服务：

```php
// 在服务提供者中注册单例
$this->app->singleton(ChannelManager::class, fn ($app) => new ChannelManager($app));
```

`singleton` 方法的实现原理：

```php
public function singleton($abstract, $concrete = null)
{
    $this->bind($abstract, $concrete, true); // 第三个参数 true 表示共享实例
}
```

### 3. 单例模式的核心实现机制

在 `bind` 方法中，当 `$shared` 参数为 `true` 时，服务会被注册为单例：

```php
public function bind($abstract, $concrete = null, $shared = false)
{
    // ... 参数处理逻辑
    
    $this->bindings[$abstract] = ['concrete' => $concrete, 'shared' => $shared];
    
    // ... 其他逻辑
}
```

当解析服务时，容器会检查是否为单例，如果是则返回已存在的实例：

```php
protected function resolve($abstract, $parameters = [], $raiseEvents = true)
{
    // 如果是单例且已解析过，直接返回实例
    if (isset($this->instances[$abstract])) {
        return $this->instances[$abstract];
    }
    
    // ... 解析逻辑
}
```

### 4. 全局访问点

Laravel 提供了多种访问单例实例的方式：

**通过 app() 辅助函数：**
```php
$app = app(); // 获取容器单例
$config = app('config'); // 获取配置单例
```

**通过 Container::getInstance()：**
```php
use Illuminate\Container\Container;

$container = Container::getInstance();
$logger = $container->make('log');
```

**通过门面模式：**
```php
use Illuminate\Support\Facades\Config;

$value = Config::get('app.name');
```

## 实际应用场景

### 1. 配置管理
```php
// config/app.php 中的服务注册
'singletons' => [
    'config' => [Illuminate\Config\Repository::class, []],
],

// 使用方式
$value = config('app.timezone');
```

### 2. 事件调度器
```php
// EventServiceProvider 中的注册
$this->app->singleton('events', function ($app) {
    return new Dispatcher($app);
});

// 使用方式
event(new UserRegistered($user));
```

### 3. 日志系统
```php
// LogServiceProvider 中的注册
$this->app->singleton('log', fn ($app) => new LogManager($app));

// 使用方式
logger()->info('User logged in', ['user_id' => $user->id]);
```

### 4. 数据库连接
```php
// 数据库管理器单例
$this->app->singleton('db', function ($app) {
    return new DatabaseManager($app, $app['db.factory']);
});
```

## 源码分析要点

### 1. 单例的生命周期管理

Laravel 容器通过 `instances` 数组来维护单例实例：

```php
protected $instances = [];
```

当服务被解析为单例时，实例会被存储在这个数组中，后续请求都会返回同一个实例。

### 2. 单例的重置机制

在测试或特定场景下，可能需要重置单例实例：

```php
public function forgetInstance($abstract)
{
    unset($this->instances[$abstract], $this->aliases[$abstract]);
}
```

### 3. 属性级别的单例支持

Laravel 12 引入了属性级别的单例声明：

```php
use Illuminate\Container\Attributes\Singleton;

#[Singleton]
class CacheManager
{
    // 这个类会自动被注册为单例
}
```

## 最佳实践

1. **合理使用单例**：只在真正需要全局唯一实例时使用单例模式
2. **避免状态污染**：单例对象应尽量保持无状态或只读状态
3. **考虑测试性**：单例可能增加测试复杂度，合理使用依赖注入
4. **线程安全**：在并发环境下确保单例的线程安全性

## 与其他模式的关系

- **与工厂模式**：单例模式常与工厂模式结合，确保工厂实例的唯一性
- **与门面模式**：门面模式通常基于单例服务提供简化接口
- **与依赖注入**：单例模式是依赖注入容器的重要特性

## 总结

Laravel 的单例模式实现体现了框架设计的精髓：通过服务容器统一管理对象生命周期，既保证了灵活性，又提供了性能优化。理解这一模式对于深入掌握 Laravel 架构至关重要。