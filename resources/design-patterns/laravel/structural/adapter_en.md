# Adapter Pattern

## Overview

Convert the interface of a class into another interface clients expect. The Adapter pattern allows classes to work together that couldn't otherwise because of incompatible interfaces.

## Architecture Diagram

### Adapter Pattern Structure

```mermaid
classDiagram
    class Target {
        <<interface>>
        +request()
    }
    
    class Client {
        +main()
    }
    
    class Adapter {
        -adaptee: Adaptee
        +request()
    }
    
    class Adaptee {
        +specificRequest()
    }
    
    Client --> Target : uses
    Adapter ..|> Target : implements
    Adapter --> Adaptee : adapts
    
    note for Adapter "Converts Adaptee interface to Target interface"
```

### Laravel Cache Adapter Implementation

```mermaid
graph TB
    A[Client Code] --> B[Cache Manager]
    B --> C{Driver Type}
    C -->|Redis| D[Redis Adapter]
    C -->|File| E[File Adapter]
    C -->|Database| F[Database Adapter]
    C -->|Memory| G[Array Adapter]
    
    D --> H[Redis Client]
    E --> I[File System]
    F --> J[Database Connection]
    G --> K[PHP Array]
    
    style D fill:#e1f5fe
    style E fill:#f3e5f5
    style F fill:#fff3e0
    style G fill:#e8f5e8
```

### Adapter Pattern Flow

```mermaid
sequenceDiagram
    participant Client
    participant CacheManager
    participant RedisAdapter
    participant RedisClient
    
    Client->>CacheManager: get('key')
    CacheManager->>RedisAdapter: get('key')
    RedisAdapter->>RedisClient: get('key')
    RedisClient-->>RedisAdapter: raw_value
    RedisAdapter->>RedisAdapter: unserialize(raw_value)
    RedisAdapter-->>CacheManager: formatted_value
    CacheManager-->>Client: formatted_value
    
    note over RedisAdapter: Adapts Redis commands to Cache interface
```

## Implementation in Laravel

### 1. Cache System Adapters

Laravel's cache system uses the Adapter pattern to support multiple storage backends:

```php
// Illuminate\Cache\CacheManager.php
class CacheManager implements FactoryContract
{
    protected function createRedisDriver(array $config)
    {
        $redis = $this->app['redis'];
        $connection = $config['connection'] ?? 'default';
        
        return $this->repository(new RedisStore($redis, $this->getPrefix($config), $connection));
    }
    
    protected function createFileDriver(array $config)
    {
        return $this->repository(new FileStore($this->app['files'], $config['path']));
    }
}
```

### 2. Database Query Builder Adapters

Laravel provides adapters for different database systems:

```php
// Different database adapters implementing the same interface
class MySqlGrammar extends Grammar
{
    public function compileSelect(Builder $query)
    {
        // MySQL-specific SQL generation
    }
}

class PostgresGrammar extends Grammar  
{
    public function compileSelect(Builder $query)
    {
        // PostgreSQL-specific SQL generation
    }
}
```

### 3. Session Storage Adapters

```php
// Session storage adapters
class FileSessionHandler implements SessionHandlerInterface
{
    public function read($sessionId)
    {
        return $this->files->get($this->path.'/'.$sessionId, '');
    }
}

class DatabaseSessionHandler implements SessionHandlerInterface
{
    public function read($sessionId)
    {
        $session = $this->getQuery()->find($sessionId);
        return $session ? base64_decode($session->payload) : '';
    }
}
```