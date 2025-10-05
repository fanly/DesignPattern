# 代理模式 (Proxy Pattern)

## 概述

代理模式为其他对象提供一种代理以控制对这个对象的访问。代理对象在客户端和目标对象之间起到中介作用，可以用于控制访问、延迟加载、记录日志、缓存等。

## 设计意图

- **访问控制**：控制对真实对象的访问权限
- **延迟加载**：推迟昂贵对象的创建和初始化
- **功能增强**：在不修改原对象的情况下添加额外功能
- **保护代理**：保护真实对象不被非法访问

## Laravel 中的实现

### 1. 延迟加载代理

Laravel 的 Eloquent ORM 使用了代理模式实现延迟加载：

```php
// Illuminate\Database\Eloquent\Relations\Relation.php
abstract class Relation
{
    protected $parent;
    protected $related;
    protected $query;
    protected $eagerLoad = [];
    
    public function __construct(Builder $query, Model $parent)
    {
        $this->query = $query;
        $this->parent = $parent;
        $this->related = $query->getModel();
        
        $this->addConstraints();
    }
    
    // 代理模式：延迟加载关联数据
    public function getResults()
    {
        return $this->query->get();
    }
    
    // 代理到查询构建器的方法
    public function __call($method, $parameters)
    {
        $result = $this->query->{$method}(...$parameters);
        
        if ($result === $this->query) {
            return $this;
        }
        
        return $result;
    }
}

// 具体实现：一对一关联
class HasOne extends Relation
{
    public function getResults()
    {
        if (is_null($this->getParentKey())) {
            return null;
        }
        
        // 代理到查询构建器获取结果
        return $this->query->first() ?: $this->getDefaultFor($this->parent);
    }
    
    public function addConstraints()
    {
        if (static::$constraints) {
            $this->query->where(
                $this->getForeignKeyName(), '=', $this->getParentKey()
            );
        }
    }
}
```

### 2. 缓存代理

Laravel 的缓存系统使用代理模式：

```php
// Illuminate\Cache\Repository.php
class Repository implements CacheContract
{
    protected $store;
    protected $events;
    protected $default = 60;
    
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
    
    // 代理模式：缓存数据访问
    public function get($key, $default = null)
    {
        // 先尝试从缓存获取
        $value = $this->store->get($this->itemKey($key));
        
        if (! is_null($value)) {
            return $value;
        }
        
        // 如果缓存不存在，执行回调函数并缓存结果
        if ($default instanceof Closure) {
            return $this->handleDefaultCallback($key, $default);
        }
        
        return value($default);
    }
    
    public function remember($key, $seconds, Closure $callback)
    {
        // 缓存代理：如果缓存存在直接返回，否则执行回调并缓存
        $value = $this->get($key);
        
        if (! is_null($value)) {
            return $value;
        }
        
        $this->put($key, $value = $callback(), $seconds);
        
        return $value;
    }
    
    public function rememberForever($key, Closure $callback)
    {
        // 永久缓存代理
        $value = $this->get($key);
        
        if (! is_null($value)) {
            return $value;
        }
        
        $this->forever($key, $value = $callback());
        
        return $value;
    }
    
    // 代理到存储引擎的方法
    public function __call($method, $parameters)
    {
        return $this->store->$method(...$parameters);
    }
}
```

### 3. 门面代理

Laravel 的门面系统是代理模式的典型应用：

```php
// Illuminate\Support\Facades\Facade.php
abstract class Facade
{
    protected static $app;
    protected static $resolvedInstance = [];
    
    // 代理模式：静态方法代理到实际对象
    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();
        
        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }
        
        return $instance->$method(...$args);
    }
    
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }
    
    protected static function getFacadeAccessor()
    {
        throw new RuntimeException('Facade does not implement getFacadeAccessor method.');
    }
    
    protected static function resolveFacadeInstance($name)
    {
        if (is_object($name)) {
            return $name;
        }
        
        if (isset(static::$resolvedInstance[$name])) {
            return static::$resolvedInstance[$name];
        }
        
        if (static::$app) {
            return static::$resolvedInstance[$name] = static::$app[$name];
        }
    }
}

// 具体门面：缓存门面
class Cache extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'cache';
    }
}

// 使用示例：代理到实际的缓存对象
Cache::get('key'); // 实际上调用的是 app('cache')->get('key')
Cache::put('key', 'value', 3600); // 代理到缓存存储引擎
```

## 实际应用场景

### 1. 图片加载代理

实现图片的延迟加载和缓存代理：

```php
// 图片接口
interface ImageInterface
{
    public function display();
    public function getSize();
    public function getMetadata();
}

// 真实图片对象（创建成本高）
class HighResolutionImage implements ImageInterface
{
    private $filename;
    private $metadata;
    private $imageData;
    
    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->loadImage();
    }
    
    private function loadImage()
    {
        // 模拟昂贵的图片加载操作
        echo "Loading high resolution image: {$this->filename}\n";
        sleep(2); // 模拟加载时间
        
        $this->metadata = [
            'width' => 1920,
            'height' => 1080,
            'format' => 'JPEG',
            'size' => filesize($this->filename)
        ];
        
        $this->imageData = file_get_contents($this->filename);
    }
    
    public function display()
    {
        return "Displaying image: {$this->filename}";
    }
    
    public function getSize()
    {
        return $this->metadata['size'];
    }
    
    public function getMetadata()
    {
        return $this->metadata;
    }
}

// 图片代理（延迟加载和缓存）
class ImageProxy implements ImageInterface
{
    private $filename;
    private $realImage = null;
    private $cache = [];
    
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    
    public function display()
    {
        $this->loadImage();
        return $this->realImage->display();
    }
    
    public function getSize()
    {
        $this->loadImage();
        return $this->realImage->getSize();
    }
    
    public function getMetadata()
    {
        $this->loadImage();
        return $this->realImage->getMetadata();
    }
    
    private function loadImage()
    {
        if ($this->realImage === null) {
            // 延迟加载真实图片
            $this->realImage = new HighResolutionImage($this->filename);
        }
    }
    
    // 缓存代理功能
    public function cache()
    {
        $cacheKey = md5($this->filename);
        
        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = [
                'display' => $this->display(),
                'size' => $this->getSize(),
                'metadata' => $this->getMetadata()
            ];
        }
        
        return $this->cache[$cacheKey];
    }
}

// 图片查看器使用代理
class ImageViewer
{
    private $images = [];
    
    public function addImage($filename)
    {
        $this->images[] = new ImageProxy($filename);
    }
    
    public function displayAll()
    {
        $output = [];
        foreach ($this->images as $image) {
            $output[] = $image->display();
        }
        return $output;
    }
    
    public function getTotalSize()
    {
        $total = 0;
        foreach ($this->images as $image) {
            $total += $image->getSize();
        }
        return $total;
    }
}
```

### 2. API 访问代理

实现 API 访问的保护代理和缓存代理：

```php
// API 接口
interface ApiClientInterface
{
    public function get($endpoint, $params = []);
    public function post($endpoint, $data = []);
    public function put($endpoint, $data = []);
    public function delete($endpoint);
}

// 真实 API 客户端
class RealApiClient implements ApiClientInterface
{
    private $baseUrl;
    private $apiKey;
    private $rateLimit = 100; // 每分钟请求限制
    
    public function __construct($baseUrl, $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->apiKey = $apiKey;
    }
    
    public function get($endpoint, $params = [])
    {
        $url = $this->buildUrl($endpoint, $params);
        return $this->makeRequest('GET', $url);
    }
    
    public function post($endpoint, $data = [])
    {
        $url = $this->buildUrl($endpoint);
        return $this->makeRequest('POST', $url, $data);
    }
    
    private function buildUrl($endpoint, $params = [])
    {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        return $url;
    }
    
    private function makeRequest($method, $url, $data = [])
    {
        // 模拟 API 请求
        echo "Making {$method} request to: {$url}\n";
        
        $options = [
            'http' => [
                'method' => $method,
                'header' => "Authorization: Bearer {$this->apiKey}\r\n",
                'timeout' => 30
            ]
        ];
        
        if ($method === 'POST' || $method === 'PUT') {
            $options['http']['header'] .= "Content-Type: application/json\r\n";
            $options['http']['content'] = json_encode($data);
        }
        
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        
        return json_decode($response, true);
    }
}

// API 访问代理（保护代理 + 缓存代理）
class ApiProxy implements ApiClientInterface
{
    private $realClient;
    private $cache = [];
    private $requestCount = 0;
    private $lastResetTime;
    private $maxRequestsPerMinute = 60;
    
    public function __construct($baseUrl, $apiKey)
    {
        $this->realClient = new RealApiClient($baseUrl, $apiKey);
        $this->lastResetTime = time();
    }
    
    public function get($endpoint, $params = [])
    {
        // 保护代理：检查访问频率限制
        $this->checkRateLimit();
        
        // 缓存代理：检查缓存
        $cacheKey = $this->getCacheKey('GET', $endpoint, $params);
        
        if (isset($this->cache[$cacheKey])) {
            echo "Returning cached response for: {$endpoint}\n";
            return $this->cache[$cacheKey];
        }
        
        // 执行真实请求
        $response = $this->realClient->get($endpoint, $params);
        
        // 缓存结果
        $this->cache[$cacheKey] = $response;
        $this->requestCount++;
        
        return $response;
    }
    
    public function post($endpoint, $data = [])
    {
        $this->checkRateLimit();
        
        // POST 请求通常不缓存
        $response = $this->realClient->post($endpoint, $data);
        $this->requestCount++;
        
        return $response;
    }
    
    public function put($endpoint, $data = [])
    {
        $this->checkRateLimit();
        $response = $this->realClient->put($endpoint, $data);
        $this->requestCount++;
        return $response;
    }
    
    public function delete($endpoint)
    {
        $this->checkRateLimit();
        $response = $this->realClient->delete($endpoint);
        $this->requestCount++;
        return $response;
    }
    
    private function checkRateLimit()
    {
        $currentTime = time();
        
        // 每分钟重置计数器
        if ($currentTime - $this->lastResetTime >= 60) {
            $this->requestCount = 0;
            $this->lastResetTime = $currentTime;
        }
        
        if ($this->requestCount >= $this->maxRequestsPerMinute) {
            throw new RateLimitException('API rate limit exceeded');
        }
    }
    
    private function getCacheKey($method, $endpoint, $params)
    {
        return md5($method . $endpoint . serialize($params));
    }
    
    public function clearCache()
    {
        $this->cache = [];
    }
    
    public function getRequestCount()
    {
        return $this->requestCount;
    }
}
```

## 源码分析要点

### 1. 代理模式的核心结构

```php
// 主题接口
interface Subject
{
    public function request();
}

// 真实主题
class RealSubject implements Subject
{
    public function request()
    {
        return "RealSubject: Handling request.";
    }
}

// 代理类
class Proxy implements Subject
{
    private $realSubject;
    
    public function __construct(RealSubject $realSubject)
    {
        $this->realSubject = $realSubject;
    }
    
    public function request()
    {
        if ($this->checkAccess()) {
            $result = $this->realSubject->request();
            $this->logAccess();
            return $result;
        }
        
        return "Proxy: Access denied";
    }
    
    private function checkAccess()
    {
        echo "Proxy: Checking access prior to firing a real request.\n";
        return true;
    }
    
    private function logAccess()
    {
        echo "Proxy: Logging the time of request.\n";
    }
}
```

### 2. Laravel 中的代理应用

Laravel 的服务容器使用代理模式实现延迟加载：

```php
// Illuminate\Container\Container.php
class Container implements ContainerInterface
{
    protected $bindings = [];
    protected $instances = [];
    protected $resolved = [];
    
    public function bind($abstract, $concrete = null, $shared = false)
    {
        $this->bindings[$abstract] = compact('concrete', 'shared');
    }
    
    public function singleton($abstract, $concrete = null)
    {
        $this->bind($abstract, $concrete, true);
    }
    
    public function make($abstract, array $parameters = [])
    {
        // 如果已经解析过，直接返回实例
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }
        
        // 延迟解析服务
        $object = $this->resolve($abstract, $parameters);
        
        // 如果是单例，缓存实例
        if ($this->isShared($abstract)) {
            $this->instances[$abstract] = $object;
        }
        
        $this->resolved[$abstract] = true;
        
        return $object;
    }
    
    protected function resolve($abstract, $parameters = [])
    {
        $concrete = $this->getConcrete($abstract);
        
        // 如果是闭包，执行闭包
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        
        // 创建新的实例
        return $this->build($concrete);
    }
}
```

## 最佳实践

### 1. 合理使用代理模式

**适用场景：**
- 需要控制对象访问时（保护代理）
- 需要延迟加载昂贵对象时（虚拟代理）
- 需要添加额外功能而不修改原对象时（装饰代理）
- 需要缓存对象结果时（缓存代理）

**不适用场景：**
- 对象创建成本不高时
- 不需要访问控制时
- 性能要求极高的场景

### 2. Laravel 中的代理实践

**数据库查询代理：**
```php
class QueryProxy
{
    private $query;
    private $cache;
    
    public function __construct($query, $cache)
    {
        $this->query = $query;
        $this->cache = $cache;
    }
    
    public function get($columns = ['*'])
    {
        $cacheKey = $this->getCacheKey($columns);
        
        if ($this->cache->has($cacheKey)) {
            return $this->cache->get($cacheKey);
        }
        
        $results = $this->query->get($columns);
        $this->cache->put($cacheKey, $results, 3600);
        
        return $results;
    }
    
    private function getCacheKey($columns)
    {
        return md5($this->query->toSql() . serialize($this->query->getBindings()) . serialize($columns));
    }
}
```

**文件访问代理：**
```php
class FileAccessProxy
{
    private $realFile;
    private $permissions;
    
    public function __construct($filename, $permissions)
    {
        $this->realFile = new RealFile($filename);
        $this->permissions = $permissions;
    }
    
    public function read()
    {
        if (!$this->checkPermission('read')) {
            throw new AccessDeniedException('Read permission denied');
        }
        
        return $this->realFile->read();
    }
    
    public function write($content)
    {
        if (!$this->checkPermission('write')) {
            throw new AccessDeniedException('Write permission denied');
        }
        
        return $this->realFile->write($content);
    }
    
    private function checkPermission($operation)
    {
        return in_array($operation, $this->permissions);
    }
}
```

## 与其他模式的关系

### 1. 与装饰器模式

代理模式控制访问，装饰器模式添加功能：

```php
// 代理模式：控制访问
class ImageProxy 
{
    private $realImage;
    
    public function display() 
    {
        if ($this->checkAccess()) {
            $this->realImage->display();
        }
    }
    
    private function checkAccess() 
    {
        return true; // 访问控制逻辑
    }
}

// 装饰器模式：添加功能
class ImageDecorator 
{
    private $image;
    
    public function __construct(Image $image) 
    {
        $this->image = $image;
    }
    
    public function display() 
    {
        $this->addBorder();
        $this->image->display();
        $this->addWatermark();
    }
    
    private function addBorder() {}
    private function addWatermark() {}
}
```

### 2. 与适配器模式

代理模式控制访问，适配器模式转换接口：

```php
// 代理模式：控制访问
class ServiceProxy 
{
    private $realService;
    
    public function request() 
    {
        $this->logRequest();
        return $this->realService->request();
    }
}

// 适配器模式：转换接口
class ServiceAdapter 
{
    private $legacyService;
    
    public function request() 
    {
        return $this->legacyService->oldRequestMethod();
    }
}
```

## 性能考虑

### 1. 代理开销

代理模式会增加方法调用开销：

```php
// 优化：减少不必要的代理调用
class OptimizedProxy
{
    private $realObject;
    private $cache = [];
    private $methodCallCount = [];
    
    public function __call($method, $args)
    {
        // 缓存频繁调用的方法结果
        if ($this->isFrequentlyCalled($method)) {
            $cacheKey = $this->getCacheKey($method, $args);
            
            if (isset($this->cache[$cacheKey])) {
                return $this->cache[$cacheKey];
            }
            
            $result = call_user_func_array([$this->realObject, $method], $args);
            $this->cache[$cacheKey] = $result;
            
            return $result;
        }
        
        return call_user_func_array([$this->realObject, $method], $args);
    }
    
    private function isFrequentlyCalled($method)
    {
        return ($this->methodCallCount[$method] ?? 0) > 10;
    }
}
```

### 2. 内存使用优化

代理对象可能增加内存使用：

```php
// 轻量级代理实现
class LightweightProxy
{
    private $realObject;
    private $lazyLoaded = false;
    
    public function __construct($className, $constructorArgs)
    {
        $this->className = $className;
        $this->constructorArgs = $constructorArgs;
    }
    
    public function __call($method, $args)
    {
        if (!$this->lazyLoaded) {
            $this->realObject = new $this->className(...$this->constructorArgs);
            $this->lazyLoaded = true;
        }
        
        return call_user_func_array([$this->realObject, $method], $args);
    }
}
```

## 总结

代理模式是 Laravel 框架中广泛使用的设计模式，它通过中介对象来控制对真实对象的访问。这种模式在 ORM 关联、缓存系统、门面系统等多个核心组件中都有体现。

代理模式的优势在于：
- **访问控制**：可以控制对真实对象的访问权限
- **延迟加载**：推迟昂贵对象的创建和初始化
- **功能增强**：可以添加额外的功能而不修改原对象
- **保护机制**：保护真实对象不被非法访问

在 Laravel 开发中，合理使用代理模式可以创建出更加安全、高效的系统，特别是在处理资源密集型操作和需要访问控制的场景时。