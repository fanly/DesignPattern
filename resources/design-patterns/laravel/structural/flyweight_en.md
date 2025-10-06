# Flyweight Pattern

## Overview

Use sharing to support large numbers of fine-grained objects efficiently. The Flyweight pattern minimizes memory usage by sharing efficiently among similar objects.

## Architecture Diagram

### Flyweight Pattern Structure

```mermaid
classDiagram
    class FlyweightFactory {
        -flyweights
        +getFlyweight(key)
    }
    
    class Flyweight {
        <<interface>>
        +operation(extrinsicState)
    }
    
    class ConcreteFlyweight {
        -intrinsicState
        +operation(extrinsicState)
    }
    
    class Context {
        -extrinsicState
        -flyweight
        +operation()
    }
    
    FlyweightFactory --> Flyweight : creates/manages
    Flyweight <|.. ConcreteFlyweight : implements
    Context --> Flyweight : uses
    
    note for ConcreteFlyweight "Stores intrinsic state"
    note for Context "Stores extrinsic state"
```

### Laravel Route Flyweight

```mermaid
graph TB
    A[Route Collection] --> B[Shared Route Objects]
    B --> C[Route Pattern 1]
    B --> D[Route Pattern 2]
    B --> E[Route Pattern 3]
    
    C --> F[Multiple Instances]
    D --> G[Multiple Instances]
    E --> H[Multiple Instances]
    
    F --> I[Different Parameters]
    G --> I
    H --> I
    
    style B fill:#e1f5fe
    style I fill:#f3e5f5
```

## Implementation in Laravel

### 1. Route Sharing

Laravel shares route patterns to reduce memory usage:

```php
// Illuminate\Routing\RouteCollection.php
class RouteCollection implements Countable, IteratorAggregate
{
    protected $routes = [];
    protected $allRoutes = [];
    protected $nameList = [];
    protected $actionList = [];
    
    public function add(Route $route)
    {
        $this->addToCollections($route);
        $this->addLookups($route);
        
        return $route;
    }
    
    protected function addToCollections($route)
    {
        $domainAndUri = $route->getDomain().$route->uri();
        
        foreach ($route->methods() as $method) {
            $this->routes[$method][$domainAndUri] = $route;
        }
        
        $this->allRoutes[$method.$domainAndUri] = $route;
    }
}
```

### 2. Validation Rule Flyweights

```php
// Shared validation rule instances
class ValidationRuleFactory
{
    protected static $instances = [];
    
    public static function make($rule)
    {
        if (!isset(static::$instances[$rule])) {
            static::$instances[$rule] = static::createRule($rule);
        }
        
        return static::$instances[$rule];
    }
    
    protected static function createRule($rule)
    {
        switch ($rule) {
            case 'required':
                return new RequiredRule();
            case 'email':
                return new EmailRule();
            case 'numeric':
                return new NumericRule();
            default:
                throw new InvalidArgumentException("Unknown rule: {$rule}");
        }
    }
}
```