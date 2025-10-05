# 桥接模式 (Bridge Pattern)

## 概述

桥接模式将抽象部分与它的实现部分分离，使它们都可以独立地变化。它通过组合的方式建立两个类层次结构之间的桥梁。

## 设计意图

- **分离抽象与实现**：将抽象接口与其实现解耦
- **独立变化**：抽象和实现可以独立扩展
- **组合优于继承**：使用组合关系代替多重继承
- **灵活性**：可以在运行时切换实现

## Laravel 中的实现

### 1. 数据库连接桥接

Laravel 的数据库连接系统使用了桥接模式：

```php
// Illuminate\Database\Connection.php (抽象接口)
abstract class Connection implements ConnectionInterface
{
    protected $pdo;
    protected $queryGrammar;
    protected $postProcessor;
    
    public function __construct($pdo, $database = '', $tablePrefix = '', array $config = [])
    {
        $this->pdo = $pdo;
        $this->database = $database;
        $this->tablePrefix = $tablePrefix;
        $this->config = $config;
        
        // 桥接到具体的语法处理器
        $this->useDefaultQueryGrammar();
        $this->useDefaultPostProcessor();
    }
    
    abstract public function getDriverName();
    
    protected function useDefaultQueryGrammar()
    {
        $this->queryGrammar = $this->getDefaultQueryGrammar();
    }
    
    protected function useDefaultPostProcessor()
    {
        $this->postProcessor = $this->getDefaultPostProcessor();
    }
}

// 具体实现：MySQL 连接
class MySqlConnection extends Connection
{
    public function getDriverName()
    {
        return 'mysql';
    }
    
    protected function getDefaultQueryGrammar()
    {
        return new MySqlGrammar;
    }
    
    protected function getDefaultPostProcessor()
    {
        return new MySqlProcessor;
    }
}

// 具体实现：PostgreSQL 连接
class PostgresConnection extends Connection
{
    public function getDriverName()
    {
        return 'pgsql';
    }
    
    protected function getDefaultQueryGrammar()
    {
        return new PostgresGrammar;
    }
    
    protected function getDefaultPostProcessor()
    {
        return new PostgresProcessor;
    }
}
```

### 2. 缓存驱动桥接

Laravel 的缓存系统也使用了桥接模式：

```php
// Illuminate\Cache\Repository.php (抽象)
class Repository
{
    protected $store;
    
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
    
    public function get($key, $default = null)
    {
        return $this->store->get($key, $default);
    }
    
    public function put($key, $value, $seconds)
    {
        return $this->store->put($key, $value, $seconds);
    }
    
    // 其他缓存操作方法...
}

// 具体实现：Redis 存储
class RedisStore implements Store
{
    protected $redis;
    
    public function __construct($redis)
    {
        $this->redis = $redis;
    }
    
    public function get($key, $default = null)
    {
        $value = $this->redis->get($key);
        return $value !== null ? $value : $default;
    }
    
    public function put($key, $value, $seconds)
    {
        return $this->redis->setex($key, $seconds, $value);
    }
}

// 具体实现：文件存储
class FileStore implements Store
{
    protected $files;
    protected $directory;
    
    public function __construct(Filesystem $files, $directory)
    {
        $this->files = $files;
        $this->directory = $directory;
    }
    
    public function get($key, $default = null)
    {
        $path = $this->path($key);
        
        if (! $this->files->exists($path)) {
            return $default;
        }
        
        $expire = substr($contents = $this->files->get($path), 0, 10);
        
        if (time() >= $expire) {
            $this->forget($key);
            return $default;
        }
        
        return unserialize(substr($contents, 10));
    }
}
```

### 3. 邮件发送桥接

Laravel 的邮件系统使用桥接模式支持多种邮件驱动：

```php
// Illuminate\Mail\Mailer.php (抽象)
class Mailer
{
    protected $swift;
    protected $views;
    
    public function __construct($name, Swift_Mailer $swift, $views)
    {
        $this->name = $name;
        $this->swift = $swift;
        $this->views = $views;
    }
    
    public function send($view, array $data, $callback)
    {
        // 构建消息
        list($view, $plain) = $this->parseView($view);
        
        $data['message'] = $message = $this->createMessage();
        
        // 桥接到具体的邮件传输实现
        $this->callMessageBuilder($callback, $message);
        
        // 发送邮件
        return $this->sendSwiftMessage($message->getSwiftMessage());
    }
    
    abstract protected function getSwiftMailer();
}

// 具体实现：SMTP 邮件
class SmtpMailer extends Mailer
{
    protected function getSwiftMailer()
    {
        return new Swift_Mailer(new Swift_SmtpTransport(
            $this->config['host'], 
            $this->config['port'], 
            $this->config['encryption']
        ));
    }
}

// 具体实现：Sendmail 邮件
class SendmailMailer extends Mailer
{
    protected function getSwiftMailer()
    {
        return new Swift_Mailer(new Swift_SendmailTransport(
            $this->config['sendmail']
        ));
    }
}
```

## 实际应用场景

### 1. 支付网关桥接

实现多支付网关的桥接模式：

```php
// 支付抽象接口
interface PaymentGateway
{
    public function pay($amount, array $data);
    public function refund($transactionId, $amount);
    public function query($transactionId);
}

// 支付实现：支付宝
class AlipayGateway implements PaymentGateway
{
    private $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function pay($amount, array $data)
    {
        // 调用支付宝支付API
        $alipay = new AlipayClient($this->config);
        return $alipay->createOrder($amount, $data);
    }
    
    public function refund($transactionId, $amount)
    {
        $alipay = new AlipayClient($this->config);
        return $alipay->refund($transactionId, $amount);
    }
}

// 支付实现：微信支付
class WechatPayGateway implements PaymentGateway
{
    private $config;
    
    public function __construct(array $config)
    {
        $this->config = $config;
    }
    
    public function pay($amount, array $data)
    {
        // 调用微信支付API
        $wechat = new WechatPayClient($this->config);
        return $wechat->unifiedOrder($amount, $data);
    }
    
    public function refund($transactionId, $amount)
    {
        $wechat = new WechatPayClient($this->config);
        return $wechat->refund($transactionId, $amount);
    }
}

// 支付服务抽象
abstract class PaymentService
{
    protected $gateway;
    
    public function __construct(PaymentGateway $gateway)
    {
        $this->gateway = $gateway;
    }
    
    abstract public function processPayment($amount, $data);
    abstract public function processRefund($transactionId, $amount);
}

// 具体支付服务
class OrderPaymentService extends PaymentService
{
    public function processPayment($amount, $data)
    {
        // 订单支付逻辑
        $result = $this->gateway->pay($amount, $data);
        
        // 记录支付日志
        $this->logPayment($result);
        
        return $result;
    }
    
    public function processRefund($transactionId, $amount)
    {
        $result = $this->gateway->refund($transactionId, $amount);
        $this->logRefund($result);
        return $result;
    }
}
```

### 2. 文件存储桥接

实现多存储后端的桥接模式：

```php
// 存储接口
interface StorageDriver
{
    public function put($path, $contents, $options = []);
    public function get($path);
    public function delete($path);
    public function exists($path);
}

// 本地存储实现
class LocalStorageDriver implements StorageDriver
{
    private $root;
    
    public function __construct($root)
    {
        $this->root = $root;
    }
    
    public function put($path, $contents, $options = [])
    {
        $fullPath = $this->root . '/' . $path;
        return file_put_contents($fullPath, $contents);
    }
    
    public function get($path)
    {
        $fullPath = $this->root . '/' . $path;
        return file_exists($fullPath) ? file_get_contents($fullPath) : null;
    }
}

// 云存储实现
class CloudStorageDriver implements StorageDriver
{
    private $client;
    private $bucket;
    
    public function __construct($client, $bucket)
    {
        $this->client = $client;
        $this->bucket = $bucket;
    }
    
    public function put($path, $contents, $options = [])
    {
        return $this->client->putObject([
            'Bucket' => $this->bucket,
            'Key' => $path,
            'Body' => $contents
        ]);
    }
    
    public function get($path)
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $path
            ]);
            return $result['Body'];
        } catch (Exception $e) {
            return null;
        }
    }
}

// 存储服务抽象
abstract class StorageService
{
    protected $driver;
    
    public function __construct(StorageDriver $driver)
    {
        $this->driver = $driver;
    }
    
    abstract public function storeFile($file, $path);
    abstract public function retrieveFile($path);
    abstract public function deleteFile($path);
}

// 具体存储服务
class DocumentStorageService extends StorageService
{
    public function storeFile($file, $path)
    {
        // 文件验证逻辑
        if (! $this->validateFile($file)) {
            throw new InvalidFileException('Invalid file format');
        }
        
        // 使用桥接的存储驱动保存文件
        return $this->driver->put($path, file_get_contents($file));
    }
    
    public function retrieveFile($path)
    {
        return $this->driver->get($path);
    }
    
    public function deleteFile($path)
    {
        return $this->driver->delete($path);
    }
}
```

## 源码分析要点

### 1. 桥接模式的核心结构

```php
// 抽象类定义接口
abstract class Abstraction
{
    protected $implementor;
    
    public function __construct(Implementor $imp)
    {
        $this->implementor = $imp;
    }
    
    abstract public function operation();
}

// 具体抽象类
class RefinedAbstraction extends Abstraction
{
    public function operation()
    {
        // 调用实现者的方法
        return $this->implementor->operationImpl();
    }
}

// 实现者接口
interface Implementor
{
    public function operationImpl();
}

// 具体实现者
class ConcreteImplementorA implements Implementor
{
    public function operationImpl()
    {
        return "ConcreteImplementorA operation";
    }
}

class ConcreteImplementorB implements Implementor
{
    public function operationImpl()
    {
        return "ConcreteImplementorB operation";
    }
}
```

### 2. Laravel 中的桥接应用

Laravel 框架中桥接模式的典型应用：

```php
// 数据库连接管理器
class DatabaseManager
{
    protected $app;
    protected $connections = [];
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultConnection();
        
        if (! isset($this->connections[$name])) {
            // 桥接到具体的数据库连接
            $this->connections[$name] = $this->makeConnection($name);
        }
        
        return $this->connections[$name];
    }
    
    protected function makeConnection($name)
    {
        $config = $this->configuration($name);
        
        // 根据配置选择具体的连接实现
        return $this->createConnector($config)->connect($config);
    }
    
    protected function createConnector(array $config)
    {
        if (! isset($config['driver'])) {
            throw new InvalidArgumentException('A driver must be specified.');
        }
        
        // 桥接到具体的连接器
        return match ($config['driver']) {
            'mysql' => new MySqlConnector,
            'pgsql' => new PostgresConnector,
            'sqlite' => new SQLiteConnector,
            'sqlsrv' => new SqlServerConnector,
            default => throw new InvalidArgumentException("Unsupported driver [{$config['driver']}]"),
        };
    }
}
```

## 最佳实践

### 1. 合理使用桥接模式

**适用场景：**
- 抽象和实现都需要独立扩展时
- 需要在运行时切换实现时
- 避免多层继承时
- 系统有多个变化维度时

**不适用场景：**
- 抽象和实现之间是固定关系时
- 实现变化很少时
- 系统维度变化较少时

### 2. Laravel 中的桥接实践

**配置驱动的桥接：**
```php
class ServiceManager
{
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();
        
        return $this->drivers[$name] ??= $this->createDriver($name);
    }
    
    protected function createDriver($name)
    {
        $config = $this->configFor($name);
        
        // 根据配置桥接到具体实现
        $driverMethod = 'create'.ucfirst($name).'Driver';
        
        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($config);
        }
        
        throw new InvalidArgumentException("Driver [{$name}] not supported.");
    }
}
```

**依赖注入桥接：**
```php
class Application
{
    public function bind($abstract, $concrete = null, $shared = false)
    {
        // 绑定抽象到具体实现
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }
    
    public function make($abstract, array $parameters = [])
    {
        // 解析依赖并桥接到具体实现
        return $this->resolve($abstract, $parameters);
    }
}
```

## 与其他模式的关系

### 1. 与适配器模式

桥接模式关注抽象与实现的分离，适配器模式关注接口的兼容：

```php
// 桥接模式：分离抽象和实现
interface Renderer 
{
    public function renderCircle($radius);
}

class VectorRenderer implements Renderer 
{
    public function renderCircle($radius) 
    {
        echo "Drawing a circle of radius {$radius} with vectors";
    }
}

class Shape 
{
    protected $renderer;
    
    public function __construct(Renderer $renderer) 
    {
        $this->renderer = $renderer;
    }
}
```

### 2. 与策略模式

桥接模式关注结构分离，策略模式关注算法替换：

```php
// 桥接模式：结构分离
abstract class Message 
{
    protected $sender;
    
    public function __construct(MessageSender $sender) 
    {
        $this->sender = $sender;
    }
    
    abstract public function send();
}

// 策略模式：算法替换
class Notification 
{
    protected $strategies = [];
    
    public function addStrategy(NotificationStrategy $strategy) 
    {
        $this->strategies[] = $strategy;
    }
    
    public function send($message) 
    {
        foreach ($this->strategies as $strategy) {
            $strategy->send($message);
        }
    }
}
```

## 性能考虑

### 1. 对象创建开销

桥接模式涉及多个对象创建，需要注意性能：

```php
// 使用对象池优化桥接模式
class ConnectionPool 
{
    protected $pool = [];
    
    public function getConnection($type) 
    {
        if (!isset($this->pool[$type])) {
            $this->pool[$type] = $this->createConnection($type);
        }
        
        return $this->pool[$type];
    }
}
```

### 2. 内存使用优化

桥接模式可能增加内存使用，需要合理设计：

```php
// 轻量级桥接实现
trait BridgeTrait 
{
    protected $implementation;
    
    public function setImplementation($impl) 
    {
        $this->implementation = $impl;
    }
    
    public function operation() 
    {
        return $this->implementation->operationImpl();
    }
}
```

## 总结

桥接模式是 Laravel 框架中广泛使用的设计模式，它通过分离抽象和实现来提供更大的灵活性。这种模式在数据库连接、缓存系统、邮件发送等多个核心组件中都有体现。

桥接模式的优势在于：
- **解耦抽象与实现**：两者可以独立变化
- **扩展性**：易于添加新的抽象或实现
- **灵活性**：运行时可以切换实现
- **符合开闭原则**：对扩展开放，对修改关闭

在 Laravel 开发中，合理使用桥接模式可以创建出结构清晰、易于维护的代码，特别是在处理多平台、多驱动的系统时。