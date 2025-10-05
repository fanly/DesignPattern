# 观察者模式 (Observer Pattern)

## 概述

定义对象间的一种一对多的依赖关系，当一个对象的状态发生改变时，所有依赖于它的对象都得到通知并被自动更新。

## 设计意图

- **松耦合**：减少主题和观察者之间的依赖关系
- **动态关系**：允许在运行时添加和移除观察者
- **事件驱动架构**：支持基于事件的通信
- **广播通知**：实现一对多的通信模式

## Laravel 中的实现

### 1. Laravel 事件系统

Laravel 的事件系统是观察者模式的复杂实现：

```php
// Illuminate\Events\Dispatcher.php
class Dispatcher implements EventDispatcher
{
    protected $listeners = [];
    protected $wildcards = [];
    
    public function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (str_contains($event, '*')) {
                $this->setupWildcardListen($event, $listener);
            } else {
                $this->listeners[$event][] = $this->makeListener($listener);
            }
        }
    }
    
    public function dispatch($event, $payload = [], $halt = false)
    {
        // 当给定的事件是对象时，我们假设它是事件对象并使用类名作为事件名称
        [$event, $payload] = $this->parseEventAndPayload($event, $payload);
        
        $responses = [];
        
        foreach ($this->getListeners($event) as $listener) {
            $response = $listener($event, $payload);
            
            // 如果从监听器返回了响应并且启用了事件停止
            if ($halt && ! is_null($response)) {
                return $response;
            }
            
            // 如果监听器返回 false，停止传播
            if ($response === false) {
                break;
            }
            
            $responses[] = $response;
        }
        
        return $halt ? null : $responses;
    }
    
    protected function makeListener($listener)
    {
        if (is_string($listener)) {
            return $this->createClassListener($listener);
        }
        
        return function ($event, $payload) use ($listener) {
            return $listener(...array_values($payload));
        };
    }
}
```

### 2. 事件类示例

```php
// 示例事件类
class OrderShipped extends Event
{
    use SerializesModels;
    
    public $order;
    
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}

// 对应的监听器
class SendShipmentNotification implements ShouldQueue
{
    public function handle(OrderShipped $event)
    {
        // 向客户发送通知
        $event->order->user->notify(new ShipmentNotification($event->order));
    }
}
```

### 3. 模型事件

Laravel 模型也通过事件实现观察者模式：

```php
// Illuminate\Database\Eloquent\Model.php
class Model
{
    protected static $dispatcher;
    
    public static function boot()
    {
        static::bootTraits();
        
        foreach (static::getObservableEvents() as $event) {
            static::registerModelEvent($event, function ($model) use ($event) {
                if (static::$dispatcher) {
                    return static::$dispatcher->dispatch("eloquent.{$event}: ".get_class($model), $model);
                }
            });
        }
    }
    
    public static function creating($callback)
    {
        static::registerModelEvent('creating', $callback);
    }
    
    public static function created($callback)
    {
        static::registerModelEvent('created', $callback);
    }
    
    public static function updating($callback)
    {
        static::registerModelEvent('updating', $callback);
    }
    
    public static function updated($callback)
    {
        static::registerModelEvent('updated', $callback);
    }
}
```

## 实际应用场景

### 1. 用户活动跟踪

```php
// 事件：用户执行了某个操作
class UserActivityPerformed extends Event
{
    public $user;
    public $activity;
    public $timestamp;
    
    public function __construct(User $user, string $activity)
    {
        $this->user = $user;
        $this->activity = $activity;
        $this->timestamp = now();
    }
}

// 观察者/监听器
class LogUserActivity implements ShouldQueue
{
    public function handle(UserActivityPerformed $event)
    {
        ActivityLog::create([
            'user_id' => $event->user->id,
            'activity' => $event->activity,
            'performed_at' => $event->timestamp,
        ]);
    }
}

class UpdateUserStatistics implements ShouldQueue
{
    public function handle(UserActivityPerformed $event)
    {
        $stats = UserStatistics::firstOrCreate(['user_id' => $event->user->id]);
        $stats->increment('total_activities');
        $stats->last_activity = $event->timestamp;
        $stats->save();
    }
}

class SendRealTimeNotification
{
    public function handle(UserActivityPerformed $event)
    {
        // 向管理仪表板发送实时通知
        broadcast(new UserActivityEvent($event->user, $event->activity));
    }
}

// 使用
event(new UserActivityPerformed($user, 'logged_in'));
```

### 2. 电商订单处理

```php
// 订单状态变更事件
class OrderStatusChanged extends Event
{
    public $order;
    public $oldStatus;
    public $newStatus;
    
    public function __construct(Order $order, string $oldStatus, string $newStatus)
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}

// 订单状态变更的多个观察者
class SendOrderStatusNotification
{
    public function handle(OrderStatusChanged $event)
    {
        // 向客户发送邮件/SMS通知
        $event->order->user->notify(new OrderStatusUpdated(
            $event->order, 
            $event->newStatus
        ));
    }
}

class UpdateInventory implements ShouldQueue
{
    public function handle(OrderStatusChanged $event)
    {
        if ($event->newStatus === 'shipped') {
            // 减少已发货商品的库存
            foreach ($event->order->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }
        }
    }
}

class TriggerFulfillmentProcess
{
    public function handle(OrderStatusChanged $event)
    {
        if ($event->newStatus === 'paid') {
            // 启动履行流程
            FulfillmentService::process($event->order);
        }
    }
}

class UpdateAnalytics
{
    public function handle(OrderStatusChanged $event)
    {
        // 更新业务分析
        Analytics::track('order_status_change', [
            'order_id' => $event->order->id,
            'from_status' => $event->oldStatus,
            'to_status' => $event->newStatus,
        ]);
    }
}
```

### 3. 缓存失效系统

```php
// 缓存失效事件
class CacheInvalidated extends Event
{
    public $tags;
    public $keys;
    public $reason;
    
    public function __construct(array $tags = [], array $keys = [], string $reason = '')
    {
        $this->tags = $tags;
        $this->keys = $keys;
        $this->reason = $reason;
    }
}

// 缓存观察者
class ClearTaggedCache
{
    public function handle(CacheInvalidated $event)
    {
        if (!empty($event->tags)) {
            Cache::tags($event->tags)->flush();
        }
    }
}

class ClearSpecificKeys
{
    public function handle(CacheInvalidated $event)
    {
        foreach ($event->keys as $key) {
            Cache::forget($key);
        }
    }
}

class LogCacheInvalidation
{
    public function handle(CacheInvalidated $event)
    {
        Log::info('缓存已失效', [
            'tags' => $event->tags,
            'keys' => $event->keys,
            'reason' => $event->reason,
        ]);
    }
}

class WarmRelatedCache
{
    public function handle(CacheInvalidated $event)
    {
        // 预热相关缓存条目
        if (in_array('products', $event->tags)) {
            CacheWarmingService::warmProductCache();
        }
    }
}
```

## 源码分析要点

### 1. 观察者模式实现细节

Laravel 的事件系统展示了观察者模式的几个高级特性：

```php
// 事件服务提供者注册
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderShipped::class => [
            SendShipmentNotification::class,
            UpdateInventory::class,
        ],
    ];
    
    protected $subscribe = [
        UserEventSubscriber::class,
    ];
}

// 事件订阅者示例
class UserEventSubscriber
{
    public function handleUserLogin($event) {}
    
    public function handleUserLogout($event) {}
    
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Login',
            [UserEventSubscriber::class, 'handleUserLogin']
        );
        
        $events->listen(
            'Illuminate\Auth\Events\Logout', 
            [UserEventSubscriber::class, 'handleUserLogout']
        );
    }
}
```

### 2. 队列事件监听器

Laravel 支持队列事件监听器以提高性能：

```php
class ProcessPayment implements ShouldQueue
{
    public $queue = 'payments';
    public $delay = 60;
    
    public function handle(PaymentProcessed $event)
    {
        // 繁重的支付处理逻辑
        PaymentProcessor::process($event->payment);
    }
    
    public function failed(PaymentProcessed $event, $exception)
    {
        // 处理失败
        $event->payment->update(['status' => 'failed']);
    }
}
```

### 3. 事件广播

Laravel 可以将事件广播到前端应用：

```php
class OrderStatusChanged implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;
    
    public $order;
    
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    
    public function broadcastOn()
    {
        return new Channel('orders.' . $this->order->id);
    }
    
    public function broadcastWith()
    {
        return [
            'status' => $this->order->status,
            'updated_at' => $this->order->updated_at->toISOString(),
        ];
    }
}
```

## 最佳实践

### 1. 何时使用观察者模式

**适用场景：**
- 当对象间需要一对多关系时
- 当一个对象的变更需要引起其他对象变更时
- 用于实现事件驱动架构
- 当想要减少组件间耦合度时

**不适用场景：**
- 当主题只有很少的观察者时
- 当观察者需要了解太多关于主题的信息时
- 对于简单的回调机制

### 2. Laravel 事件最佳实践

**使用事件处理横切关注点：**
```php
// 良好：使用事件分离关注点
class UserController
{
    public function register(UserRegistrationRequest $request)
    {
        $user = User::create($request->validated());
        
        // 触发事件而不是在控制器中处理所有事情
        event(new UserRegistered($user));
        
        return response()->json($user, 201);
    }
}

// 避免：把所有事情放在控制器中
class UserController
{
    public function register(UserRegistrationRequest $request)
    {
        $user = User::create($request->validated());
        
        // 不要这样做 - 职责过多
        Mail::to($user)->send(new WelcomeEmail($user));
        Analytics::track('user_registered', $user);
        Cache::forget('users_count');
        // ... 更多逻辑
        
        return response()->json($user, 201);
    }
}
```

**事件命名约定：**
```php
// 对已经发生的事件使用过去式
UserRegistered::class      // 良好
RegisterUser::class        // 避免 - 听起来像命令

OrderShipped::class        // 良好
ShipOrder::class           // 避免
```

### 3. 测试事件驱动系统

**测试事件监听器：**
```php
class OrderProcessingTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_order_shipped_event_triggers_notification()
    {
        Event::fake();
        
        $order = Order::factory()->create();
        
        // 执行应该触发事件的操作
        $order->update(['status' => 'shipped']);
        
        // 断言事件已分发
        Event::assertDispatched(OrderShipped::class, function ($event) use ($order) {
            return $event->order->id === $order->id;
        });
    }
    
    public function test_listener_handles_event_correctly()
    {
        $order = Order::factory()->create();
        $listener = new SendShipmentNotification();
        
        $listener->handle(new OrderShipped($order));
        
        // 断言通知已发送
        Notification::assertSentTo(
            $order->user, 
            ShipmentNotification::class
        );
    }
}
```

## 性能考虑

### 1. 事件系统性能

Laravel 的事件系统针对性能进行了优化：

```php
// 事件被缓存以获得更好的性能
protected function getListeners($eventName)
{
    if (isset($this->listeners[$eventName])) {
        return $this->listeners[$eventName];
    }
    
    // 通配符匹配逻辑...
}
```

### 2. 队列监听器

对于性能密集型的监听器，使用队列：

```php
class ProcessLargeDataset implements ShouldQueue
{
    public $tries = 3;
    public $timeout = 300;
    
    public function handle(LargeDatasetProcessed $event)
    {
        // 在后台进行繁重处理
        DataProcessor::process($event->dataset);
    }
}
```

### 3. 事件缓存

Laravel 缓存事件监听器以提高性能：

```php
// 事件发现被缓存
php artisan event:cache

// 需要时清除事件缓存
php artisan event:clear
```

## 总结

观察者模式通过 Laravel 的事件系统深度集成到 Laravel 架构中。这种实现提供了一种强大、灵活的方式来构建解耦的事件驱动应用，同时保持了优异的性能特性。

Laravel 的事件系统展示了观察者模式如何从简单的本地事件扩展到具有队列监听器和实时广播的复杂分布式系统。