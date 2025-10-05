# Abstract Factory Pattern

## Overview

Provide an interface for creating families of related or dependent objects without specifying their concrete classes. The Abstract Factory pattern provides an interface for creating families of related products.

## Architecture Diagram

### Abstract Factory Pattern Structure

```mermaid
classDiagram
    class AbstractFactory {
        <<interface>>
        +createProductA(): AbstractProductA
        +createProductB(): AbstractProductB
    }
    
    class ConcreteFactory1 {
        +createProductA(): AbstractProductA
        +createProductB(): AbstractProductB
    }
    
    class ConcreteFactory2 {
        +createProductA(): AbstractProductA
        +createProductB(): AbstractProductB
    }
    
    class AbstractProductA {
        <<interface>>
        +operation(): void
    }
    
    class AbstractProductB {
        <<interface>>
        +operation(): void
    }
    
    class ProductA1 {
        +operation(): void
    }
    
    class ProductA2 {
        +operation(): void
    }
    
    class ProductB1 {
        +operation(): void
    }
    
    class ProductB2 {
        +operation(): void
    }
    
    AbstractFactory <|.. ConcreteFactory1 : implements
    AbstractFactory <|.. ConcreteFactory2 : implements
    AbstractProductA <|.. ProductA1 : implements
    AbstractProductA <|.. ProductA2 : implements
    AbstractProductB <|.. ProductB1 : implements
    AbstractProductB <|.. ProductB2 : implements
    
    ConcreteFactory1 --> ProductA1 : creates
    ConcreteFactory1 --> ProductB1 : creates
    ConcreteFactory2 --> ProductA2 : creates
    ConcreteFactory2 --> ProductB2 : creates
```

### Laravel Database Factory Family

```mermaid
graph TB
    A[Database Factory] --> B[MySQL Factory]
    A --> C[PostgreSQL Factory]
    A --> D[SQLite Factory]
    
    B --> E[MySQL Connection]
    B --> F[MySQL Grammar]
    B --> G[MySQL Schema Builder]
    
    C --> H[PostgreSQL Connection]
    C --> I[PostgreSQL Grammar]
    C --> J[PostgreSQL Schema Builder]
    
    D --> K[SQLite Connection]
    D --> L[SQLite Grammar]
    D --> M[SQLite Schema Builder]
    
    style A fill:#e1f5fe
    style B fill:#f3e5f5
    style C fill:#fff3e0
    style D fill:#e8f5e8
```

### Factory Creation Flow

```mermaid
sequenceDiagram
    participant Client
    participant DatabaseManager
    participant MySQLFactory
    participant Connection
    participant Grammar
    participant SchemaBuilder
    
    Client->>DatabaseManager: connection('mysql')
    DatabaseManager->>MySQLFactory: create(config)
    MySQLFactory->>Connection: new MySqlConnection()
    MySQLFactory->>Grammar: new MySqlGrammar()
    MySQLFactory->>SchemaBuilder: new MySqlBuilder()
    
    MySQLFactory-->>DatabaseManager: {connection, grammar, builder}
    DatabaseManager-->>Client: configured connection
    
    note over MySQLFactory: Creates family of related MySQL objects
```

## Implementation in Laravel

### 1. Database Connection Factory

Laravel's database system uses Abstract Factory pattern to create families of database-related objects:

```php
// Illuminate\Database\Connectors\ConnectionFactory.php
class ConnectionFactory
{
    public function make(array $config, $name = null)
    {
        $config = $this->parseConfig($config, $name);
        
        if (isset($config['read'])) {
            return $this->createReadWriteConnection($config);
        }
        
        return $this->createSingleConnection($config);
    }
    
    protected function createConnection($driver, $connection, $database, $prefix = '', array $config = [])
    {
        if ($resolver = Connection::getResolver($driver)) {
            return $resolver($connection, $database, $prefix, $config);
        }
        
        switch ($driver) {
            case 'mysql':
                return new MySqlConnection($connection, $database, $prefix, $config);
            case 'pgsql':
                return new PostgresConnection($connection, $database, $prefix, $config);
            case 'sqlite':
                return new SQLiteConnection($connection, $database, $prefix, $config);
        }
    }
}
```

### 2. Validation Factory

```php
// Illuminate\Validation\Factory.php
class Factory implements FactoryContract
{
    public function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        $validator = $this->resolve($data, $rules, $messages, $customAttributes);
        
        if (! is_null($this->verifier)) {
            $validator->setPresenceVerifier($this->verifier);
        }
        
        if (! is_null($this->container)) {
            $validator->setContainer($this->container);
        }
        
        $this->addExtensions($validator);
        
        return $validator;
    }
    
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
        if (is_null($this->resolver)) {
            return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
        }
        
        return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
    }
}
```

### 3. Broadcasting Factory

```php
// Illuminate\Broadcasting\BroadcastManager.php
class BroadcastManager extends Manager
{
    protected function createPusherDriver(array $config)
    {
        return new PusherBroadcaster(
            new Pusher(
                $config['key'],
                $config['secret'],
                $config['app_id'],
                $config['options'] ?? []
            )
        );
    }
    
    protected function createRedisDriver(array $config)
    {
        return new RedisBroadcaster(
            $this->app->make('redis'), $config['connection'] ?? null
        );
    }
}
```