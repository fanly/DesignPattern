# Bridge Pattern

## Overview

Decouple an abstraction from its implementation so that the two can vary independently. The Bridge pattern separates an abstraction from its implementation so that both can be modified independently.

## Architecture Diagram

### Bridge Pattern Structure

```mermaid
classDiagram
    class Abstraction {
        -implementor: Implementor
        +operation()
    }
    
    class RefinedAbstraction {
        +operation()
    }
    
    class Implementor {
        <<interface>>
        +operationImpl()
    }
    
    class ConcreteImplementorA {
        +operationImpl()
    }
    
    class ConcreteImplementorB {
        +operationImpl()
    }
    
    Abstraction <|-- RefinedAbstraction : extends
    Abstraction --> Implementor : uses
    Implementor <|.. ConcreteImplementorA : implements
    Implementor <|.. ConcreteImplementorB : implements
    
    note for Abstraction "Maintains reference to Implementor"
    note for Implementor "Defines implementation interface"
```

### Laravel Database Bridge

```mermaid
graph TB
    A[Query Builder] --> B[Grammar Interface]
    B --> C[MySQL Grammar]
    B --> D[PostgreSQL Grammar]
    B --> E[SQLite Grammar]
    
    C --> F[MySQL SQL]
    D --> G[PostgreSQL SQL]
    E --> H[SQLite SQL]
    
    style A fill:#e1f5fe
    style B fill:#f3e5f5
    style F fill:#fff3e0
    style G fill:#fff3e0
    style H fill:#fff3e0
```

## Implementation in Laravel

### 1. Database Query Grammar Bridge

Laravel uses the Bridge pattern to separate query building from SQL generation:

```php
// Illuminate\Database\Query\Builder.php (Abstraction)
class Builder
{
    protected $grammar;
    protected $processor;
    
    public function __construct(ConnectionInterface $connection, Grammar $grammar = null, Processor $processor = null)
    {
        $this->connection = $connection;
        $this->grammar = $grammar ?: $connection->getQueryGrammar();
        $this->processor = $processor ?: $connection->getPostProcessor();
    }
    
    public function toSql()
    {
        return $this->grammar->compileSelect($this);
    }
}

// Illuminate\Database\Query\Grammars\Grammar.php (Implementor)
abstract class Grammar
{
    abstract public function compileSelect(Builder $query);
    abstract public function compileInsert(Builder $query, array $values);
    abstract public function compileUpdate(Builder $query, array $values);
    abstract public function compileDelete(Builder $query);
}

// Concrete implementations
class MySqlGrammar extends Grammar
{
    public function compileSelect(Builder $query)
    {
        // MySQL-specific SQL generation
        return trim($this->concatenate($this->compileComponents($query)));
    }
}

class PostgresGrammar extends Grammar
{
    public function compileSelect(Builder $query)
    {
        // PostgreSQL-specific SQL generation
        return trim($this->concatenate($this->compileComponents($query)));
    }
}
```

### 2. Cache Store Bridge

```php
// Cache manager bridges different storage implementations
class CacheManager extends Manager implements FactoryContract
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