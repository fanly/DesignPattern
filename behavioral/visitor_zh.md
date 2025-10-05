# 访问者模式 (Visitor Pattern)

## 概述

访问者模式是一种将算法与对象结构分离的设计模式。它允许在不修改现有对象结构的情况下定义新的操作。访问者模式通过将操作封装在访问者对象中，实现了操作与对象结构的解耦。

## 设计意图

- **操作与结构分离**：将算法从对象结构中分离出来
- **开闭原则**：易于添加新的操作，无需修改现有结构
- **双重分派**：根据访问者和被访问者的类型执行相应操作
- **集中操作**：将相关操作集中在一个访问者类中

## Laravel 中的实现

### 1. 路由访问者

Laravel 的路由系统使用访问者模式处理不同类型的路由：

```php
// Illuminate\Routing\Route.php
class Route
{
    protected $uri;
    protected $methods;
    protected $action;
    protected $compiled;
    
    public function __construct($methods, $uri, $action)
    {
        $this->methods = (array) $methods;
        $this->uri = $uri;
        $this->action = $action;
    }
    
    // 接受访问者
    public function accept(RouteVisitor $visitor)
    {
        return $visitor->visitRoute($this);
    }
    
    public function getUri()
    {
        return $this->uri;
    }
    
    public function getMethods()
    {
        return $this->methods;
    }
    
    public function getAction()
    {
        return $this->action;
    }
}

// 路由访问者接口
interface RouteVisitor
{
    public function visitRoute(Route $route);
    public function visitResourceRoute(ResourceRoute $route);
    public function visitApiRoute(ApiRoute $route);
}

// 具体访问者：路由编译访问者
class RouteCompilerVisitor implements RouteVisitor
{
    public function visitRoute(Route $route)
    {
        // 编译普通路由
        $route->compiled = (new RouteCompiler)->compile($route);
        return $route->compiled;
    }
    
    public function visitResourceRoute(ResourceRoute $route)
    {
        // 编译资源路由
        $compiledRoutes = [];
        
        foreach ($route->getResourceMethods() as $method => $uri) {
            $compiledRoute = (new RouteCompiler)->compileResourceRoute($route, $method, $uri);
            $compiledRoutes[$method] = $compiledRoute;
        }
        
        return $compiledRoutes;
    }
    
    public function visitApiRoute(ApiRoute $route)
    {
        // 编译 API 路由（添加版本前缀等）
        $route->uri = 'api/v' . $route->getVersion() . '/' . ltrim($route->uri, '/');
        return $this->visitRoute($route);
    }
}

// 具体访问者：路由验证访问者
class RouteValidatorVisitor implements RouteVisitor
{
    public function visitRoute(Route $route)
    {
        // 验证普通路由
        $this->validateUri($route->uri);
        $this->validateMethods($route->methods);
        $this->validateAction($route->action);
        
        return true;
    }
    
    public function visitResourceRoute(ResourceRoute $route)
    {
        // 验证资源路由的所有方法
        foreach ($route->getResourceMethods() as $method => $uri) {
            $this->validateUri($uri);
            $this->validateMethod($method);
        }
        
        return true;
    }
    
    public function visitApiRoute(ApiRoute $route)
    {
        // 验证 API 路由的特殊要求
        $this->validateApiVersion($route->getVersion());
        return $this->visitRoute($route);
    }
    
    private function validateUri($uri) {}
    private function validateMethods($methods) {}
    private function validateAction($action) {}
    private function validateApiVersion($version) {}
}
```

### 2. Eloquent 模型访问者

Laravel 的 Eloquent ORM 使用访问者模式处理模型操作：

```php
// Illuminate\Database\Eloquent\Model.php
class Model implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    protected $attributes = [];
    protected $original = [];
    protected $changes = [];
    
    // 接受访问者
    public function accept(ModelVisitor $visitor)
    {
        return $visitor->visitModel($this);
    }
    
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    public function getOriginal($key = null)
    {
        return $key ? $this->original[$key] : $this->original;
    }
    
    public function getChanges()
    {
        return $this->changes;
    }
}

// 模型访问者接口
interface ModelVisitor
{
    public function visitModel(Model $model);
    public function visitUserModel(User $user);
    public function visitProductModel(Product $product);
}

// 具体访问者：模型序列化访问者
class ModelSerializerVisitor implements ModelVisitor
{
    public function visitModel(Model $model)
    {
        // 序列化普通模型
        return [
            'attributes' => $model->getAttributes(),
            'relations' => $this->serializeRelations($model),
            'timestamps' => $this->serializeTimestamps($model)
        ];
    }
    
    public function visitUserModel(User $user)
    {
        // 序列化用户模型（特殊处理敏感信息）
        $data = $this->visitModel($user);
        
        // 移除敏感信息
        unset($data['attributes']['password']);
        unset($data['attributes']['remember_token']);
        
        // 添加用户特定信息
        $data['user_info'] = [
            'roles' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name')
        ];
        
        return $data;
    }
    
    public function visitProductModel(Product $product)
    {
        // 序列化产品模型
        $data = $this->visitModel($product);
        
        // 添加产品特定信息
        $data['product_info'] = [
            'categories' => $product->categories->pluck('name'),
            'images' => $product->images->pluck('url'),
            'inventory' => $product->getInventoryStatus()
        ];
        
        return $data;
    }
    
    private function serializeRelations($model) {}
    private function serializeTimestamps($model) {}
}

// 具体访问者：模型验证访问者
class ModelValidatorVisitor implements ModelVisitor
{
    public function visitModel(Model $model)
    {
        // 验证普通模型
        $rules = $model->getValidationRules();
        $validator = Validator::make($model->getAttributes(), $rules);
        
        return $validator->passes();
    }
    
    public function visitUserModel(User $user)
    {
        // 验证用户模型（特殊规则）
        $rules = array_merge($user->getValidationRules(), [
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|min:8'
        ]);
        
        $validator = Validator::make($user->getAttributes(), $rules);
        return $validator->passes();
    }
    
    public function visitProductModel(Product $product)
    {
        // 验证产品模型
        $rules = array_merge($product->getValidationRules(), [
            'sku' => 'required|unique:products,sku,' . $product->id,
            'price' => 'required|numeric|min:0'
        ]);
        
        $validator = Validator::make($product->getAttributes(), $rules);
        return $validator->passes();
    }
}
```

### 3. 事件访问者

Laravel 的事件系统使用访问者模式处理不同类型的事件：

```php
// Illuminate\Events\Dispatcher.php
class Dispatcher
{
    protected $listeners = [];
    protected $wildcards = [];
    
    // 接受访问者处理事件
    public function dispatch($event, $payload = [], $halt = false)
    {
        $visitor = $this->getEventVisitor($event);
        return $visitor->visitEvent($event, $payload, $halt);
    }
    
    private function getEventVisitor($event)
    {
        if ($event instanceof ModelEvent) {
            return new ModelEventVisitor($this);
        } elseif ($event instanceof MailEvent) {
            return new MailEventVisitor($this);
        } elseif ($event instanceof NotificationEvent) {
            return new NotificationEventVisitor($this);
        } else {
            return new GenericEventVisitor($this);
        }
    }
}

// 事件访问者接口
interface EventVisitor
{
    public function visitEvent($event, $payload, $halt);
}

// 具体访问者：模型事件访问者
class ModelEventVisitor implements EventVisitor
{
    private $dispatcher;
    
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function visitEvent($event, $payload, $halt)
    {
        // 处理模型事件（created, updated, deleted等）
        $model = $payload[0] ?? null;
        
        if ($model instanceof Model) {
            $this->handleModelEvent($event, $model);
        }
        
        return $this->dispatcher->fireEvent($event, $payload, $halt);
    }
    
    private function handleModelEvent($event, $model)
    {
        switch ($event) {
            case 'eloquent.created':
                $this->onModelCreated($model);
                break;
            case 'eloquent.updated':
                $this->onModelUpdated($model);
                break;
            case 'eloquent.deleted':
                $this->onModelDeleted($model);
                break;
        }
    }
    
    private function onModelCreated($model) {}
    private function onModelUpdated($model) {}
    private function onModelDeleted($model) {}
}

// 具体访问者：邮件事件访问者
class MailEventVisitor implements EventVisitor
{
    private $dispatcher;
    
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function visitEvent($event, $payload, $halt)
    {
        // 处理邮件事件
        $mail = $payload[0] ?? null;
        
        if ($mail instanceof Mailable) {
            $this->handleMailEvent($event, $mail);
        }
        
        return $this->dispatcher->fireEvent($event, $payload, $halt);
    }
    
    private function handleMailEvent($event, $mail)
    {
        switch ($event) {
            case 'mail.sending':
                $this->onMailSending($mail);
                break;
            case 'mail.sent':
                $this->onMailSent($mail);
                break;
            case 'mail.failed':
                $this->onMailFailed($mail);
                break;
        }
    }
    
    private function onMailSending($mail) {}
    private function onMailSent($mail) {}
    private function onMailFailed($mail) {}
}
```

## 实际应用场景

### 1. 报表生成访问者

实现不同类型的报表生成：

```php
// 报表元素接口
interface ReportElement
{
    public function accept(ReportVisitor $visitor);
}

// 具体报表元素
class SalesData implements ReportElement
{
    private $sales;
    private $period;
    
    public function __construct($sales, $period)
    {
        $this->sales = $sales;
        $this->period = $period;
    }
    
    public function accept(ReportVisitor $visitor)
    {
        return $visitor->visitSalesData($this);
    }
    
    public function getSales()
    {
        return $this->sales;
    }
    
    public function getPeriod()
    {
        return $this->period;
    }
}

class InventoryData implements ReportElement
{
    private $inventory;
    private $categories;
    
    public function __construct($inventory, $categories)
    {
        $this->inventory = $inventory;
        $this->categories = $categories;
    }
    
    public function accept(ReportVisitor $visitor)
    {
        return $visitor->visitInventoryData($this);
    }
    
    public function getInventory()
    {
        return $this->inventory;
    }
    
    public function getCategories()
    {
        return $this->categories;
    }
}

class CustomerData implements ReportElement
{
    private $customers;
    private $demographics;
    
    public function __construct($customers, $demographics)
    {
        $this->customers = $customers;
        $this->demographics = $demographics;
    }
    
    public function accept(ReportVisitor $visitor)
    {
        return $visitor->visitCustomerData($this);
    }
    
    public function getCustomers()
    {
        return $this->customers;
    }
    
    public function getDemographics()
    {
        return $this->demographics;
    }
}

// 报表访问者接口
interface ReportVisitor
{
    public function visitSalesData(SalesData $data);
    public function visitInventoryData(InventoryData $data);
    public function visitCustomerData(CustomerData $data);
}

// 具体访问者：PDF报表生成器
class PdfReportVisitor implements ReportVisitor
{
    public function visitSalesData(SalesData $data)
    {
        // 生成销售数据的PDF报表
        $pdf = new PdfGenerator();
        
        $content = [
            'title' => 'Sales Report - ' . $data->getPeriod(),
            'data' => $data->getSales(),
            'charts' => $this->generateSalesCharts($data->getSales())
        ];
        
        return $pdf->generate($content);
    }
    
    public function visitInventoryData(InventoryData $data)
    {
        // 生成库存数据的PDF报表
        $pdf = new PdfGenerator();
        
        $content = [
            'title' => 'Inventory Report',
            'data' => $data->getInventory(),
            'categories' => $data->getCategories(),
            'tables' => $this->generateInventoryTables($data->getInventory())
        ];
        
        return $pdf->generate($content);
    }
    
    public function visitCustomerData(CustomerData $data)
    {
        // 生成客户数据的PDF报表
        $pdf = new PdfGenerator();
        
        $content = [
            'title' => 'Customer Analytics Report',
            'data' => $data->getCustomers(),
            'demographics' => $data->getDemographics(),
            'analysis' => $this->generateCustomerAnalysis($data->getCustomers())
        ];
        
        return $pdf->generate($content);
    }
    
    private function generateSalesCharts($sales) {}
    private function generateInventoryTables($inventory) {}
    private function generateCustomerAnalysis($customers) {}
}

// 具体访问者：Excel报表生成器
class ExcelReportVisitor implements ReportVisitor
{
    public function visitSalesData(SalesData $data)
    {
        // 生成销售数据的Excel报表
        $excel = new ExcelGenerator();
        
        $sheets = [
            'Sales Summary' => $this->createSalesSummarySheet($data->getSales()),
            'Sales by Period' => $this->createPeriodSheet($data->getSales(), $data->getPeriod()),
            'Charts' => $this->createChartsSheet($data->getSales())
        ];
        
        return $excel->generate($sheets);
    }
    
    public function visitInventoryData(InventoryData $data)
    {
        // 生成库存数据的Excel报表
        $excel = new ExcelGenerator();
        
        $sheets = [
            'Inventory Overview' => $this->createInventoryOverviewSheet($data->getInventory()),
            'Category Breakdown' => $this->createCategorySheet($data->getInventory(), $data->getCategories()),
            'Stock Alerts' => $this->createAlertsSheet($data->getInventory())
        ];
        
        return $excel->generate($sheets);
    }
    
    public function visitCustomerData(CustomerData $data)
    {
        // 生成客户数据的Excel报表
        $excel = new ExcelGenerator();
        
        $sheets = [
            'Customer List' => $this->createCustomerListSheet($data->getCustomers()),
            'Demographic Analysis' => $this->createDemographicSheet($data->getDemographics()),
            'Customer Segments' => $this->createSegmentsSheet($data->getCustomers())
        ];
        
        return $excel->generate($sheets);
    }
    
    private function createSalesSummarySheet($sales) {}
    private function createPeriodSheet($sales, $period) {}
    private function createChartsSheet($sales) {}
}

// 报表生成器
class ReportGenerator
{
    private $elements = [];
    
    public function addElement(ReportElement $element)
    {
        $this->elements[] = $element;
    }
    
    public function generateReport(ReportVisitor $visitor)
    {
        $results = [];
        
        foreach ($this->elements as $element) {
            $results[] = $element->accept($visitor);
        }
        
        return $results;
    }
}
```

### 2. 代码分析访问者

实现代码质量分析工具：

```php
// 代码元素接口
interface CodeElement
{
    public function accept(CodeVisitor $visitor);
}

// 具体代码元素
class ClassElement implements CodeElement
{
    private $name;
    private $methods;
    private $properties;
    private $namespace;
    
    public function __construct($name, $methods, $properties, $namespace)
    {
        $this->name = $name;
        $this->methods = $methods;
        $this->properties = $properties;
        $this->namespace = $namespace;
    }
    
    public function accept(CodeVisitor $visitor)
    {
        return $visitor->visitClass($this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getMethods()
    {
        return $this->methods;
    }
    
    public function getProperties()
    {
        return $this->properties;
    }
    
    public function getNamespace()
    {
        return $this->namespace;
    }
}

class FunctionElement implements CodeElement
{
    private $name;
    private $parameters;
    private $returnType;
    private $body;
    
    public function __construct($name, $parameters, $returnType, $body)
    {
        $this->name = $name;
        $this->parameters = $parameters;
        $this->returnType = $returnType;
        $this->body = $body;
    }
    
    public function accept(CodeVisitor $visitor)
    {
        return $visitor->visitFunction($this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
    public function getReturnType()
    {
        return $this->returnType;
    }
    
    public function getBody()
    {
        return $this->body;
    }
}

class InterfaceElement implements CodeElement
{
    private $name;
    private $methods;
    private $namespace;
    
    public function __construct($name, $methods, $namespace)
    {
        $this->name = $name;
        $this->methods = $methods;
        $this->namespace = $namespace;
    }
    
    public function accept(CodeVisitor $visitor)
    {
        return $visitor->visitInterface($this);
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getMethods()
    {
        return $this->methods;
    }
    
    public function getNamespace()
    {
        return $this->namespace;
    }
}

// 代码访问者接口
interface CodeVisitor
{
    public function visitClass(ClassElement $class);
    public function visitFunction(FunctionElement $function);
    public function visitInterface(InterfaceElement $interface);
}

// 具体访问者：代码质量分析器
class CodeQualityVisitor implements CodeVisitor
{
    public function visitClass(ClassElement $class)
    {
        $analysis = [
            'class_name' => $class->getName(),
            'namespace' => $class->getNamespace(),
            'metrics' => $this->analyzeClassMetrics($class),
            'issues' => $this->findClassIssues($class),
            'suggestions' => $this->generateClassSuggestions($class)
        ];
        
        return $analysis;
    }
    
    public function visitFunction(FunctionElement $function)
    {
        $analysis = [
            'function_name' => $function->getName(),
            'metrics' => $this->analyzeFunctionMetrics($function),
            'issues' => $this->findFunctionIssues($function),
            'suggestions' => $this->generateFunctionSuggestions($function)
        ];
        
        return $analysis;
    }
    
    public function visitInterface(InterfaceElement $interface)
    {
        $analysis = [
            'interface_name' => $interface->getName(),
            'namespace' => $interface->getNamespace(),
            'metrics' => $this->analyzeInterfaceMetrics($interface),
            'issues' => $this->findInterfaceIssues($interface),
            'suggestions' => $this->generateInterfaceSuggestions($interface)
        ];
        
        return $analysis;
    }
    
    private function analyzeClassMetrics($class)
    {
        $methods = $class->getMethods();
        $properties = $class->getProperties();
        
        return [
            'method_count' => count($methods),
            'property_count' => count($properties),
            'complexity' => $this->calculateComplexity($methods),
            'cohesion' => $this->calculateCohesion($methods, $properties)
        ];
    }
    
    private function analyzeFunctionMetrics($function)
    {
        return [
            'parameter_count' => count($function->getParameters()),
            'cyclomatic_complexity' => $this->calculateCyclomaticComplexity($function->getBody()),
            'lines_of_code' => $this->countLinesOfCode($function->getBody())
        ];
    }
    
    private function analyzeInterfaceMetrics($interface)
    {
        return [
            'method_count' => count($interface->getMethods()),
            'abstractness' => 1.0, // 接口完全抽象
            'stability' => $this->calculateStability($interface)
        ];
    }
    
    private function findClassIssues($class) {}
    private function findFunctionIssues($function) {}
    private function findInterfaceIssues($interface) {}
    private function generateClassSuggestions($class) {}
    private function generateFunctionSuggestions($function) {}
    private function generateInterfaceSuggestions($interface) {}
    private function calculateComplexity($methods) {}
    private function calculateCohesion($methods, $properties) {}
    private function calculateCyclomaticComplexity($body) {}
    private function countLinesOfCode($body) {}
    private function calculateStability($interface) {}
}

// 具体访问者：代码文档生成器
class DocumentationVisitor implements CodeVisitor
{
    public function visitClass(ClassElement $class)
    {
        $documentation = [
            'class' => $class->getName(),
            'namespace' => $class->getNamespace(),
            'description' => $this->generateClassDescription($class),
            'methods' => $this->generateMethodDocumentation($class->getMethods()),
            'properties' => $this->generatePropertyDocumentation($class->getProperties()),
            'examples' => $this->generateClassExamples($class)
        ];
        
        return $documentation;
    }
    
    public function visitFunction(FunctionElement $function)
    {
        $documentation = [
            'function' => $function->getName(),
            'description' => $this->generateFunctionDescription($function),
            'parameters' => $this->generateParameterDocumentation($function->getParameters()),
            'return_type' => $function->getReturnType(),
            'examples' => $this->generateFunctionExamples($function)
        ];
        
        return $documentation;
    }
    
    public function visitInterface(InterfaceElement $interface)
    {
        $documentation = [
            'interface' => $interface->getName(),
            'namespace' => $interface->getNamespace(),
            'description' => $this->generateInterfaceDescription($interface),
            'methods' => $this->generateInterfaceMethodDocumentation($interface->getMethods()),
            'implementation_guide' => $this->generateImplementationGuide($interface)
        ];
        
        return $documentation;
    }
    
    private function generateClassDescription($class) {}
    private function generateMethodDocumentation($methods) {}
    private function generatePropertyDocumentation($properties) {}
    private function generateClassExamples($class) {}
    private function generateFunctionDescription($function) {}
    private function generateParameterDocumentation($parameters) {}
    private function generateFunctionExamples($function) {}
    private function generateInterfaceDescription($interface) {}
    private function generateInterfaceMethodDocumentation($methods) {}
    private function generateImplementationGuide($interface) {}
}
```

## 源码分析要点

### 1. 访问者模式的核心结构

```php
// 元素接口
interface Element
{
    public function accept(Visitor $visitor);
}

// 具体元素A
class ConcreteElementA implements Element
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitConcreteElementA($this);
    }
    
    public function operationA()
    {
        return "ConcreteElementA operation";
    }
}

// 具体元素B
class ConcreteElementB implements Element
{
    public function accept(Visitor $visitor)
    {
        $visitor->visitConcreteElementB($this);
    }
    
    public function operationB()
    {
        return "ConcreteElementB operation";
    }
}

// 访问者接口
interface Visitor
{
    public function visitConcreteElementA(ConcreteElementA $element);
    public function visitConcreteElementB(ConcreteElementB $element);
}

// 具体访问者1
class ConcreteVisitor1 implements Visitor
{
    public function visitConcreteElementA(ConcreteElementA $element)
    {
        echo "ConcreteVisitor1: " . $element->operationA() . "\n";
    }
    
    public function visitConcreteElementB(ConcreteElementB $element)
    {
        echo "ConcreteVisitor1: " . $element->operationB() . "\n";
    }
}

// 具体访问者2
class ConcreteVisitor2 implements Visitor
{
    public function visitConcreteElementA(ConcreteElementA $element)
    {
        echo "ConcreteVisitor2: " . $element->operationA() . "\n";
    }
    
    public function visitConcreteElementB(ConcreteElementB $element)
    {
        echo "ConcreteVisitor2: " . $element->operationB() . "\n";
    }
}

// 对象结构
class ObjectStructure
{
    private $elements = [];
    
    public function attach(Element $element)
    {
        $this->elements[] = $element;
    }
    
    public function detach(Element $element)
    {
        $key = array_search($element, $this->elements, true);
        if ($key !== false) {
            unset($this->elements[$key]);
        }
    }
    
    public function accept(Visitor $visitor)
    {
        foreach ($this->elements as $element) {
            $element->accept($visitor);
        }
    }
}
```

### 2. Laravel 中的访问者应用

Laravel 的查询构建器使用访问者模式处理不同的数据库操作：

```php
// Illuminate\Database\Query\Builder.php
class Builder
{
    protected $grammar;
    protected $processor;
    
    public function __construct(Connection $connection, Grammar $grammar, Processor $processor)
    {
        $this->connection = $connection;
        $this->grammar = $grammar;
        $this->processor = $processor;
    }
    
    // 接受访问者处理查询
    public function accept(QueryVisitor $visitor)
    {
        return $visitor->visitBuilder($this);
    }
    
    public function getGrammar()
    {
        return $this->grammar;
    }
    
    public function getProcessor()
    {
        return $this->processor;
    }
}

// 查询访问者接口
interface QueryVisitor
{
    public function visitBuilder(Builder $builder);
    public function visitJoinClause(JoinClause $join);
    public function visitWhereClause(WhereClause $where);
}

// 具体访问者：SQL生成访问者
class SqlGeneratorVisitor implements QueryVisitor
{
    public function visitBuilder(Builder $builder)
    {
        // 生成SELECT语句
        $sql = $this->compileSelect($builder);
        return $sql;
    }
    
    public function visitJoinClause(JoinClause $join)
    {
        // 生成JOIN语句
        $sql = $this->compileJoin($join);
        return $sql;
    }
    
    public function visitWhereClause(WhereClause $where)
    {
        // 生成WHERE条件
        $sql = $this->compileWhere($where);
        return $sql;
    }
    
    private function compileSelect($builder)
    {
        // 编译SELECT查询
        $components = $this->compileComponents($builder);
        return $this->concatenate($components);
    }
    
    private function compileComponents($builder)
    {
        return [
            'aggregate' => $this->compileAggregate($builder),
            'columns' => $this->compileColumns($builder),
            'from' => $this->compileFrom($builder),
            'joins' => $this->compileJoins($builder),
            'wheres' => $this->compileWheres($builder),
            'groups' => $this->compileGroups($builder),
            'havings' => $this->compileHavings($builder),
            'orders' => $this->compileOrders($builder),
            'limit' => $this->compileLimit($builder),
            'offset' => $this->compileOffset($builder)
        ];
    }
}
```

## 最佳实践

### 1. 合理使用访问者模式

**适用场景：**
- 需要对复杂对象结构执行多种不相关的操作时
- 对象结构相对稳定，但需要频繁添加新操作时
- 需要将相关操作集中在一个类中时
- 需要跨多个类实现某个功能时

**不适用场景：**
- 对象结构经常变化时
- 操作与对象结构紧密耦合时
- 性能要求极高的场景

### 2. Laravel 中的访问者实践

**表单验证访问者：**
```php
class FormValidatorVisitor
{
    public function visitTextField(TextField $field)
    {
        return Validator::make(
            ['value' => $field->getValue()],
            ['value' => 'required|string|max:255']
        );
    }
    
    public function visitEmailField(EmailField $field)
    {
        return Validator::make(
            ['value' => $field->getValue()],
            ['value' => 'required|email|unique:users,email']
        );
    }
    
    public function visitFileField(FileField $field)
    {
        return Validator::make(
            ['file' => $field->getValue()],
            ['file' => 'required|file|max:10240|mimes:jpg,png,pdf']
        );
    }
}
```

**数据导出访问者：**
```php
class DataExporterVisitor
{
    public function visitUserData(UserData $data)
    {
        return [
            'format' => 'csv',
            'filename' => 'users_' . date('Y-m-d') . '.csv',
            'headers' => ['ID', 'Name', 'Email', 'Created At'],
            'data' => $this->formatUserData($data->getUsers())
        ];
    }
    
    public function visitProductData(ProductData $data)
    {
        return [
            'format' => 'json',
            'filename' => 'products_' . date('Y-m-d') . '.json',
            'data' => $this->formatProductData($data->getProducts())
        ];
    }
    
    public function visitOrderData(OrderData $data)
    {
        return [
            'format' => 'xml',
            'filename' => 'orders_' . date('Y-m-d') . '.xml',
            'data' => $this->formatOrderData($data->getOrders())
        ];
    }
}
```

## 与其他模式的关系

### 1. 与迭代器模式

访问者模式通常与迭代器模式结合使用：

```php
class ObjectStructure implements IteratorAggregate
{
    private $elements = [];
    
    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }
    
    public function accept(Visitor $visitor)
    {
        foreach ($this as $element) {
            $element->accept($visitor);
        }
    }
}
```

### 2. 与组合模式

访问者模式常用于遍历组合模式的结构：

```php
class CompositeElement implements Element
{
    private $children = [];
    
    public function accept(Visitor $visitor)
    {
        $visitor->visitCompositeElement($this);
        
        foreach ($this->children as $child) {
            $child->accept($visitor);
        }
    }
    
    public function add(Element $element)
    {
        $this->children[] = $element;
    }
}
```

## 性能考虑

### 1. 访问者模式的开销

访问者模式会增加方法调用开销：

```php
// 优化：批量处理元素
class BatchVisitor implements Visitor
{
    private $batchSize = 100;
    private $batchResults = [];
    
    public function visitElement(Element $element)
    {
        $this->batchResults[] = $this->processElement($element);
        
        if (count($this->batchResults) >= $this->batchSize) {
            $this->flushBatch();
        }
    }
    
    public function flushBatch()
    {
        if (!empty($this->batchResults)) {
            $this->processBatch($this->batchResults);
            $this->batchResults = [];
        }
    }
    
    private function processElement($element) {}
    private function processBatch($batch) {}
}
```

### 2. 内存使用优化

对于大型对象结构，考虑使用流式访问者：

```php
class StreamingVisitor implements Visitor
{
    public function visitLargeDataset(LargeDataset $dataset)
    {
        // 使用生成器避免内存溢出
        foreach ($dataset->stream() as $chunk) {
            yield $this->processChunk($chunk);
        }
    }
    
    private function processChunk($chunk)
    {
        // 处理数据块
        return array_map(function($item) {
            return $this->transformItem($item);
        }, $chunk);
    }
}
```

## 总结

访问者模式是23个经典设计模式中的最后一个模式。它通过将算法与对象结构分离，实现了操作与结构的解耦，符合开闭原则。

在Laravel框架中，访问者模式虽然没有像其他模式那样显式使用，但其思想在路由处理、事件分发、查询构建等多个组件中都有体现。访问者模式特别适用于需要对复杂对象结构执行多种不相关操作的场景。

至此，我们已经完成了全部的23个设计模式的文档创建，构成了完整的设计模式知识体系。