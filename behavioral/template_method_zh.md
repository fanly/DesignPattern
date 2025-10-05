# 模板方法模式 (Template Method Pattern)

## 概述

模板方法模式定义一个操作中的算法骨架，而将一些步骤延迟到子类中。模板方法使得子类可以不改变一个算法的结构即可重定义该算法的某些特定步骤。

## 设计意图

- **算法复用**：将公共的算法逻辑提取到父类中
- **扩展性**：允许子类重写特定步骤而不改变算法结构
- **控制反转**：父类控制算法流程，子类实现具体步骤
- **代码复用**：避免重复的算法代码

## Laravel 中的实现

### 1. 控制器模板方法

Laravel 的控制器基类使用了模板方法模式，定义了请求处理的基本流程：

```php
// Illuminate\Routing\Controller.php
abstract class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    
    // 模板方法：定义请求处理的基本流程
    public function callAction($method, $parameters)
    {
        // 前置处理
        $this->setupLayout();
        
        // 调用具体的动作方法（由子类实现）
        $response = call_user_func_array([$this, $method], $parameters);
        
        // 后置处理
        $this->cleanupLayout();
        
        return $response;
    }
    
    protected function setupLayout()
    {
        // 默认实现，子类可以重写
    }
    
    protected function cleanupLayout()
    {
        // 默认实现，子类可以重写
    }
}
```

### 2. 中间件模板方法

中间件的处理流程也使用了模板方法模式：

```php
// Illuminate\Pipeline\Pipeline.php
class Pipeline
{
    // 模板方法：定义中间件处理流程
    public function then(Closure $destination)
    {
        $pipeline = array_reduce(
            array_reverse($this->middleware),
            $this->carry(),
            $destination
        );
        
        return $pipeline($this->passable);
    }
    
    protected function carry()
    {
        return function ($stack, $pipe) {
            return function ($passable) use ($stack, $pipe) {
                // 具体的中间件处理逻辑（由具体的中间件类实现）
                if (is_callable($pipe)) {
                    return $pipe($passable, $stack);
                }
                
                $middleware = $this->container->make($pipe);
                
                return $middleware->handle($passable, $stack);
            };
        };
    }
}
```

### 3. 数据库迁移模板方法

数据库迁移类使用了模板方法模式定义迁移的基本流程：

```php
// Illuminate\Database\Migrations\Migration.php
abstract class Migration
{
    // 模板方法：定义迁移执行流程
    public function up()
    {
        // 具体的表创建逻辑（由子类实现）
        $this->createTables();
        
        // 索引创建（由子类实现）
        $this->createIndexes();
        
        // 数据填充（由子类实现）
        $this->seedData();
    }
    
    public function down()
    {
        // 具体的表删除逻辑（由子类实现）
        $this->dropTables();
        
        // 索引删除（由子类实现）
        $this->dropIndexes();
    }
    
    abstract protected function createTables();
    abstract protected function createIndexes();
    protected function seedData() {}
    abstract protected function dropTables();
    abstract protected function dropIndexes();
}
```

## 实际应用场景

### 1. 表单请求验证模板

自定义表单请求类使用了模板方法模式：

```php
// Illuminate\Foundation\Http\FormRequest.php
abstract class FormRequest extends Request
{
    // 模板方法：定义验证流程
    public function validateResolved()
    {
        $this->prepareForValidation();
        
        if (! $this->passesAuthorization()) {
            $this->failedAuthorization();
        }
        
        $validator = $this->getValidatorInstance();
        
        if ($validator->fails()) {
            $this->failedValidation($validator);
        }
        
        $this->passedValidation();
    }
    
    protected function prepareForValidation()
    {
        // 子类可以重写此方法进行验证前准备
    }
    
    protected function passesAuthorization()
    {
        // 子类可以重写此方法实现授权逻辑
        return method_exists($this, 'authorize') ? $this->container->call([$this, 'authorize']) : true;
    }
    
    abstract public function rules();
}
```

### 2. 任务调度模板方法

任务调度器使用了模板方法模式定义任务执行流程：

```php
// Illuminate\Console\Scheduling\Event.php
abstract class Event
{
    // 模板方法：定义任务执行流程
    public function run(Container $container)
    {
        if ($this->withoutOverlapping && ! $this->mutex->create($this)) {
            return;
        }
        
        $this->runInBackground 
            ? $this->runCommandInBackground($container)
            : $this->runCommandInForeground($container);
    }
    
    protected function runCommandInForeground(Container $container)
    {
        // 具体的命令执行逻辑
        (new Process(
            $this->buildCommand(), base_path(), null, null, null
        ))->run();
        
        $this->callAfterCallbacks($container);
    }
    
    abstract protected function buildCommand();
}
```

### 3. 视图组件模板方法

Blade 组件使用了模板方法模式：

```php
// Illuminate\View\Component.php
abstract class Component
{
    // 模板方法：定义组件渲染流程
    public function resolveView()
    {
        $view = $this->view();
        
        if ($view instanceof View) {
            return $view;
        }
        
        $data = $this->data();
        
        return view($view, $data);
    }
    
    abstract public function view();
    
    public function data()
    {
        // 默认的数据准备方法，子类可以重写
        $class = static::class;
        
        return ['attributes' => new ComponentAttributeBag];
    }
}
```

## 源码分析要点

### 1. 钩子方法的使用

Laravel 中的模板方法模式大量使用了钩子方法（Hook Methods）：

```php
// 前置钩子方法
protected function beforeExecute() {}
protected function afterExecute() {}

// 必须实现的方法
abstract protected function executeCore();

// 模板方法
public function execute()
{
    $this->beforeExecute();
    $result = $this->executeCore();
    $this->afterExecute();
    
    return $result;
}
```

### 2. 控制流程的灵活性

模板方法模式在 Laravel 中提供了灵活的流程控制：

```php
// 条件性步骤执行
protected function shouldExecuteStep()
{
    return true; // 子类可以重写此方法
}

public function templateMethod()
{
    $this->step1();
    
    if ($this->shouldExecuteStep()) {
        $this->step2();
    }
    
    $this->step3();
}
```

### 3. 异常处理模板

Laravel 的异常处理也使用了模板方法模式：

```php
// Illuminate\Foundation\Exceptions\Handler.php
class Handler
{
    // 模板方法：定义异常处理流程
    public function render($request, Throwable $e)
    {
        $e = $this->mapException($e);
        
        if ($this->shouldReport($e)) {
            $this->report($e);
        }
        
        return $this->shouldRenderJson($request, $e)
            ? $this->prepareJsonResponse($request, $e)
            : $this->prepareResponse($request, $e);
    }
    
    protected function mapException(Throwable $e)
    {
        // 子类可以重写此方法进行异常映射
        return $e;
    }
}
```

## 最佳实践

### 1. 合理使用模板方法模式

**适用场景：**
- 多个类有相同的算法结构，但具体实现不同
- 需要控制子类的扩展点
- 希望避免代码重复

**不适用场景：**
- 算法步骤经常变化
- 子类需要完全重写算法结构

### 2. Laravel 中的模板方法实践

**利用抽象类：**
```php
abstract class BaseProcessor
{
    // 模板方法
    final public function process()
    {
        $this->validate();
        $data = $this->extract();
        $result = $this->transform($data);
        return $this->load($result);
    }
    
    abstract protected function validate();
    abstract protected function extract();
    abstract protected function transform($data);
    abstract protected function load($result);
}
```

**提供默认实现：**
```php
abstract class BaseService
{
    public function execute()
    {
        $this->before();
        $result = $this->doExecute();
        $this->after();
        return $result;
    }
    
    protected function before() {}
    abstract protected function doExecute();
    protected function after() {}
}
```

### 3. 测试模板方法

**测试模板方法流程：**
```php
public function test_template_method_execution_flow()
{
    $processor = new ConcreteProcessor();
    
    $result = $processor->process();
    
    $this->assertTrue($processor->beforeCalled);
    $this->assertTrue($processor->afterCalled);
    $this->assertEquals('expected', $result);
}
```

**模拟钩子方法：**
```php
public function test_hook_methods_are_called()
{
    $mock = $this->getMockBuilder(BaseProcessor::class)
        ->setMethods(['before', 'after'])
        ->getMockForAbstractClass();
    
    $mock->expects($this->once())->method('before');
    $mock->expects($this->once())->method('after');
    
    $mock->process();
}
```

## 与其他模式的关系

### 1. 与策略模式

模板方法模式关注算法结构，策略模式关注算法实现：

```php
// 模板方法模式：控制算法流程
abstract class Processor 
{
    public function process() 
    {
        $this->step1();
        $this->step2(); // 子类实现
        $this->step3();
    }
}

// 策略模式：替换算法实现
class Context 
{
    public function setStrategy(Strategy $strategy) 
    {
        $this->strategy = $strategy;
    }
    
    public function execute() 
    {
        return $this->strategy->execute();
    }
}
```

### 2. 与工厂方法模式

模板方法模式常与工厂方法模式结合使用：

```php
abstract class Creator 
{
    // 模板方法
    public function create() 
    {
        $product = $this->factoryMethod(); // 工厂方法
        $this->configure($product);
        return $product;
    }
    
    abstract protected function factoryMethod();
    abstract protected function configure(Product $product);
}
```

### 3. 与装饰器模式

模板方法模式定义算法结构，装饰器模式动态添加功能：

```php
// 模板方法定义基本流程
abstract class BaseHandler 
{
    public function handle($request) 
    {
        $this->preHandle($request);
        $response = $this->doHandle($request);
        return $this->postHandle($response);
    }
}

// 装饰器添加额外功能
class LoggingHandler extends BaseHandler 
{
    public function handle($request) 
    {
        Log::info('Handling request');
        $response = parent::handle($request);
        Log::info('Request handled');
        return $response;
    }
}
```

## 性能考虑

### 1. 方法调用开销

模板方法模式涉及多个方法调用，在性能敏感的场景需要注意：

```php
// 优化：减少不必要的方法调用
abstract class OptimizedProcessor 
{
    public function process() 
    {
        $this->step1();
        
        // 只在需要时调用钩子方法
        if ($this->needsStep2()) {
            $this->step2();
        }
        
        $this->step3();
    }
    
    protected function needsStep2() 
    {
        return true;
    }
}
```

### 2. 内存使用

模板方法模式通常使用继承，需要注意内存使用：

```php
// 轻量级模板方法实现
trait ProcessTrait 
{
    public function process() 
    {
        $this->beforeProcess();
        $this->doProcess();
        $this->afterProcess();
    }
    
    abstract protected function doProcess();
    protected function beforeProcess() {}
    protected function afterProcess() {}
}
```

## 总结

模板方法模式是 Laravel 框架中广泛使用的设计模式，它通过定义算法的骨架而将具体步骤延迟到子类中实现。这种模式在控制器、中间件、迁移、验证等多个核心组件中都有体现。

模板方法模式的优势在于：
- **代码复用**：将公共算法逻辑提取到父类
- **扩展性**：子类可以重写特定步骤
- **控制流程**：父类控制算法执行流程
- **维护性**：算法修改只需在父类中进行

在 Laravel 开发中，合理使用模板方法模式可以创建出结构清晰、易于维护的代码，特别是在处理具有固定流程的业务逻辑时。