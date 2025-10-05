# 适配器模式 (Adapter Pattern)

## 概述

适配器模式将一个类的接口转换成客户期望的另一个接口，使得原本由于接口不兼容而不能一起工作的类可以一起工作。它充当两个不兼容接口之间的桥梁。

## 设计意图

- **接口转换**：将不兼容的接口转换为兼容的接口
- **复用现有代码**：使现有类能够与其他类协同工作
- **解耦系统**：降低系统组件之间的耦合度
- **向后兼容**：为旧系统提供新接口的支持

## Laravel 中的实现

### 1. 文件系统适配器

Laravel 的文件系统是适配器模式的经典应用。`FilesystemAdapter` 作为适配器，统一了不同存储服务的接口：

```php
// Illuminate\Filesystem\FilesystemAdapter.php
class FilesystemAdapter implements Filesystem
{
    protected $driver;
    
    public function __construct($driver)
    {
        $this->driver = $driver;
    }
    
    // 适配器方法：将不同驱动的接口统一为Filesystem接口
    public function put($path, $contents, $options = [])
    {
        return $this->driver->put($path, $contents, $options);
    }
    
    public function get($path)
    {
        return $this->driver->get($path);
    }
    
    public function exists($path)
    {
        return $this->driver->exists($path);
    }
}
```

### 2. 缓存适配器

缓存系统通过适配器统一不同缓存驱动的接口：

```php
// Illuminate\Cache\Repository.php
class Repository implements Cache
{
    protected $store;
    
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
    
    // 适配器方法：统一缓存操作接口
    public function get($key, $default = null)
    {
        return $this->store->get($key, $default);
    }
    
    public function put($key, $value, $seconds = null)
    {
        return $this->store->put($key, $value, $seconds);
    }
    
    public function forget($key)
    {
        return $this->store->forget($key);
    }
}
```

### 3. 队列适配器

队列系统适配不同的队列服务提供商：

```php
// Illuminate\Queue\Queue.php
abstract class Queue implements QueueContract
{
    // 抽象适配器：定义统一的队列接口
    abstract public function push($job, $data = '', $queue = null);
    abstract public function later($delay, $job, $data = '', $queue = null);
    abstract public function pop($queue = null);
}

// 具体适配器实现
class RedisQueue extends Queue
{
    protected $redis;
    
    public function __construct(Redis $redis, $default = 'default')
    {
        $this->redis = $redis;
        $this->default = $default;
    }
    
    // 适配Redis特定的接口到统一的队列接口
    public function push($job, $data = '', $queue = null)
    {
        return $this->pushRaw($this->createPayload($job, $data), $queue);
    }
    
    public function pushRaw($payload, $queue = null, array $options = [])
    {
        $this->redis->rpush($this->getQueue($queue), $payload);
        
        return json_decode($payload, true)['id'] ?? null;
    }
}
```

## 实际应用场景

### 1. 不同文件存储服务的适配

```php
// 本地文件系统适配
$localAdapter = new FilesystemAdapter(
    new LocalAdapter(storage_path('app'))
);

// AWS S3 适配
$s3Adapter = new FilesystemAdapter(
    new AwsS3Adapter(new S3Client($config), $config['bucket'])
);

// FTP 适配
$ftpAdapter = new FilesystemAdapter(
    new FtpAdapter($config)
);

// 统一的接口使用
$localAdapter->put('file.txt', 'content');
$s3Adapter->put('file.txt', 'content');
$ftpAdapter->put('file.txt', 'content');
```

### 2. 不同缓存驱动的适配

```php
// Redis 缓存适配
$redisStore = new RedisStore($redis, $prefix);
$redisCache = new Repository($redisStore);

// Memcached 缓存适配
$memcachedStore = new MemcachedStore($memcached, $prefix);
$memcachedCache = new Repository($memcachedStore);

// 数据库缓存适配
$databaseStore = new DatabaseStore($database, $table);
$databaseCache = new Repository($databaseStore);

// 统一的操作接口
$redisCache->put('key', 'value', 3600);
$memcachedCache->put('key', 'value', 3600);
$databaseCache->put('key', 'value', 3600);
```

### 3. 不同邮件传输服务的适配

```php
// SMTP 适配
$smtpTransport = new SmtpTransport($host, $port, $encryption);
$smtpMailer = new Mailer($smtpTransport);

// Sendmail 适配
$sendmailTransport = new SendmailTransport();
$sendmailMailer = new Mailer($sendmailTransport);

// Mailgun 适配
$mailgunTransport = new MailgunTransport($client, $domain);
$mailgunMailer = new Mailer($mailgunTransport);

// 统一的邮件发送接口
$smtpMailer->send($message);
$sendmailMailer->send($message);
$mailgunMailer->send($message);
```

## 源码分析要点

### 1. 适配器模式的结构

在 Laravel 中，适配器模式通常包含以下组件：

**目标接口（Target Interface）：**
```php
interface Filesystem
{
    public function put($path, $contents, $options = []);
    public function get($path);
    public function exists($path);
}
```

**适配器（Adapter）：**
```php
class FilesystemAdapter implements Filesystem
{
    protected $driver;
    
    public function __construct($driver)
    {
        $this->driver = $driver;
    }
    
    public function put($path, $contents, $options = [])
    {
        // 适配逻辑：将Filesystem接口转换为具体驱动的接口
        return $this->driver->put($path, $contents, $options);
    }
}
```

**被适配者（Adaptee）：**
```php
class LocalAdapter
{
    public function write($path, $contents, $config)
    {
        // 本地文件系统的特定接口
        return file_put_contents($path, $contents);
    }
}
```

### 2. 适配器的配置机制

Laravel 通过配置文件驱动适配器的选择：

```php
// config/filesystems.php
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    
    's3' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'bucket' => env('AWS_BUCKET'),
    ],
],
```

### 3. 适配器的工厂模式结合

适配器通常与工厂模式结合使用：

```php
class FilesystemManager
{
    public function createLocalDriver(array $config)
    {
        // 创建被适配者
        $adapter = new LocalAdapter($config['root']);
        
        // 创建适配器
        return new FilesystemAdapter($adapter);
    }
    
    public function createS3Driver(array $config)
    {
        // 创建被适配者
        $client = new S3Client($config);
        $adapter = new AwsS3Adapter($client, $config['bucket']);
        
        // 创建适配器
        return new FilesystemAdapter($adapter);
    }
}
```

## 最佳实践

### 1. 合理使用适配器模式

**适用场景：**
- 需要使用现有的类，但其接口与需要的接口不匹配
- 想要创建一个可以复用的类，该类可以与其他不相关的类或不可预见的类协同工作
- 需要使用几个现有的子类，但是通过子类化每个子类来适配它们的接口不现实

**不适用场景：**
- 接口本身设计不合理，应该重新设计接口而不是使用适配器
- 系统很小，直接修改代码比使用适配器更简单

### 2. Laravel 中的适配器实践

**利用服务容器进行依赖注入：**
```php
// 在服务提供者中注册适配器
$this->app->bind('filesystem.disk.local', function ($app) {
    $adapter = new LocalAdapter(storage_path('app'));
    return new FilesystemAdapter($adapter);
});

$this->app->bind('filesystem.disk.s3', function ($app) {
    $client = new S3Client(config('filesystems.disks.s3'));
    $adapter = new AwsS3Adapter($client, config('filesystems.disks.s3.bucket'));
    return new FilesystemAdapter($adapter);
});
```

**配置驱动的适配器选择：**
```php
// 根据环境选择不同的适配器
'default' => env('FILESYSTEM_DISK', 'local'),

// 为不同环境配置不同的适配器
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    
    'production' => [
        'driver' => 's3',
        'key' => env('AWS_ACCESS_KEY_ID'),
        'bucket' => env('AWS_BUCKET'),
    ],
],
```

### 3. 测试中的适配器

**模拟适配器接口：**
```php
// 在测试中模拟适配器
Storage::fake('s3');

// 测试适配器的统一接口
Storage::disk('s3')->put('file.txt', 'content');
$this->assertTrue(Storage::disk('s3')->exists('file.txt'));
```

**测试适配器的转换逻辑：**
```php
public function test_filesystem_adapter_converts_interfaces_correctly()
{
    $mockAdapter = $this->createMock(LocalAdapter::class);
    $mockAdapter->expects($this->once())
        ->method('write')
        ->with('test.txt', 'content', [])
        ->willReturn(true);
    
    $filesystem = new FilesystemAdapter($mockAdapter);
    $result = $filesystem->put('test.txt', 'content');
    
    $this->assertTrue($result);
}
```

## 与其他模式的关系

### 1. 与桥接模式

适配器模式关注接口的转换，而桥接模式关注抽象和实现的分离：

```php
// 适配器模式：接口转换
$adapter = new FilesystemAdapter($specificDriver);

// 桥接模式：抽象与实现分离
abstract class Filesystem
{
    protected $implementation;
    
    public function __construct(FilesystemImplementation $impl)
    {
        $this->implementation = $impl;
    }
}
```

### 2. 与装饰器模式

适配器改变对象的接口，而装饰器增强对象的功能：

```php
// 适配器：改变接口
class CacheAdapter implements CacheInterface
{
    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }
    
    public function get($key)
    {
        // 适配Redis接口到Cache接口
        return $this->redis->get($key);
    }
}

// 装饰器：增强功能
class LoggingCacheDecorator implements CacheInterface
{
    public function __construct(CacheInterface $cache, Logger $logger)
    {
        $this->cache = $cache;
        $this->logger = $logger;
    }
    
    public function get($key)
    {
        $this->logger->info("Getting key: {$key}");
        return $this->cache->get($key);
    }
}
```

### 3. 与外观模式

适配器使不兼容的接口能够协同工作，而外观模式提供简化的接口：

```php
// 适配器：接口转换
$adapter = new ThirdPartyServiceAdapter($thirdPartyService);

// 外观模式：简化复杂子系统
class PaymentFacade
{
    public function pay($amount)
    {
        // 简化支付流程的复杂调用
        $this->validator->validate($amount);
        $this->processor->process($amount);
        $this->notifier->notify($amount);
    }
}
```

## 性能考虑

### 1. 适配器的性能开销

适配器模式会引入一定的性能开销，主要体现在：

- **方法调用转发**：每次调用都需要转发到被适配者
- **数据转换**：可能需要进行数据格式的转换
- **对象创建**：需要创建适配器对象和被适配者对象

### 2. 优化策略

**对象复用：**
```php
// 复用适配器实例
class FilesystemManager
{
    protected $adapters = [];
    
    public function disk($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        
        // 复用已创建的适配器
        return $this->adapters[$name] ??= $this->resolve($name);
    }
}
```

**延迟创建：**
```php
// 只有在需要时才创建适配器
public function get($key)
{
    if (!isset($this->adapter)) {
        $this->adapter = $this->createAdapter();
    }
    
    return $this->adapter->get($key);
}
```

## Laravel 12 新特性

### 1. 属性驱动的适配器配置

Laravel 12 引入了属性驱动的适配器配置：

```php
use Illuminate\Filesystem\Attributes\AsFilesystemAdapter;

#[AsFilesystemAdapter('custom')]
class CustomFilesystemAdapter implements Filesystem
{
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function put($path, $contents, $options = [])
    {
        // 自定义适配器实现
        return $this->driver->store($path, $contents, $options);
    }
}
```

### 2. 基于接口的适配器注册

新的接口抽象让适配器注册更加灵活：

```php
interface FilesystemAdapterFactory
{
    public function create(array $config): Filesystem;
    public function supports(string $driver): bool;
}
```

## 总结

适配器模式是 Laravel 框架中实现多驱动支持的核心技术。通过适配器模式，Laravel 能够：

1. **统一接口**：为不同的服务提供商提供统一的编程接口
2. **支持扩展**：易于添加新的驱动和服务
3. **保持兼容**：确保新旧系统能够协同工作
4. **降低耦合**：将客户端代码与具体实现解耦

适配器模式体现了"开闭原则"和"依赖倒置原则"的精髓，是构建灵活、可扩展系统架构的重要工具。在 Laravel 的文件系统、缓存、队列、邮件等组件中，适配器模式都发挥着关键作用。