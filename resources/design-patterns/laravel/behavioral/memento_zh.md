# 备忘录模式 (Memento Pattern)

## 概述

备忘录模式在不破坏封装性的前提下，捕获一个对象的内部状态，并在该对象之外保存这个状态。这样以后就可将该对象恢复到原先保存的状态。

## 设计意图

- **状态保存**：捕获对象内部状态
- **封装保护**：不破坏对象的封装性
- **状态恢复**：支持对象状态回滚
- **历史记录**：维护对象状态历史

## Laravel 中的实现

### 1. 数据库事务备忘录

Laravel 的数据库事务系统实现了备忘录模式：

```php
// Illuminate\Database\Connection.php
class Connection implements ConnectionInterface
{
    // 开始事务（保存当前状态）
    public function beginTransaction()
    {
        $this->transactions++;
        
        if ($this->transactions == 1) {
            try {
                $this->pdo->beginTransaction();
            } catch (Exception $e) {
                $this->transactions--;
                throw $e;
            }
        } elseif ($this->transactions > 1 && $this->queryGrammar->supportsSavepoints()) {
            // 创建保存点（备忘录）
            $this->pdo->exec(
                $this->queryGrammar->compileSavepoint('trans'.($this->transactions))
            );
        }
        
        $this->fireConnectionEvent('beganTransaction');
    }
    
    // 回滚事务（恢复状态）
    public function rollBack($toLevel = null)
    {
        $toLevel = $toLevel ?? $this->transactions - 1;
        
        if ($toLevel < 0 || $toLevel >= $this->transactions) {
            return;
        }
        
        // 回滚到指定保存点
        if ($toLevel == 0) {
            $this->pdo->rollBack();
        } elseif ($this->queryGrammar->supportsSavepoints()) {
            $this->pdo->exec(
                $this->queryGrammar->compileSavepointRollBack('trans'.($toLevel + 1))
            );
        }
        
        $this->transactions = $toLevel;
        $this->fireConnectionEvent('rollingBack');
    }
    
    // 提交事务（确认状态变更）
    public function commit()
    {
        if ($this->transactions == 1) {
            $this->pdo->commit();
        }
        
        $this->transactions = max(0, $this->transactions - 1);
        $this->fireConnectionEvent('committed');
    }
}

// 使用示例
DB::beginTransaction();

try {
    // 执行数据库操作
    User::create(['name' => 'John']);
    Post::create(['title' => 'Hello World']);
    
    // 提交事务（确认状态）
    DB::commit();
} catch (Exception $e) {
    // 回滚事务（恢复状态）
    DB::rollBack();
}
```

### 2. 模型属性备忘录

Eloquent 模型的属性修改跟踪：

```php
// Illuminate\Database\Eloquent\Model.php
class Model implements ArrayAccess, Arrayable, Jsonable, JsonSerializable
{
    protected $original = [];
    protected $attributes = [];
    protected $changes = [];
    
    // 保存原始状态
    public function syncOriginal()
    {
        $this->original = $this->attributes;
        $this->changes = [];
        
        return $this;
    }
    
    // 检查属性是否被修改
    public function isDirty($attributes = null)
    {
        return $this->hasChanges(
            $this->getDirty(), is_array($attributes) ? $attributes : func_get_args()
        );
    }
    
    // 获取被修改的属性
    public function getDirty()
    {
        $dirty = [];
        
        foreach ($this->attributes as $key => $value) {
            if (! array_key_exists($key, $this->original)) {
                $dirty[$key] = $value;
            } elseif ($value !== $this->original[$key] &&
                     ! $this->originalIsNumericallyEquivalent($key)) {
                $dirty[$key] = $value;
            }
        }
        
        return $dirty;
    }
    
    // 恢复原始状态
    public function rollbackAttributes()
    {
        $this->attributes = $this->original;
        $this->changes = [];
        
        return $this;
    }
    
    // 获取变更历史
    public function getChanges()
    {
        return $this->changes;
    }
}

// 使用示例
$user = User::find(1);

// 保存原始状态
$user->syncOriginal();

// 修改属性
$user->name = 'New Name';
$user->email = 'new@email.com';

// 检查是否被修改
if ($user->isDirty(['name', 'email'])) {
    echo "用户信息已被修改";
}

// 获取修改的属性
$dirtyAttributes = $user->getDirty();

// 恢复原始状态
$user->rollbackAttributes();
```

### 3. Session 状态备忘录

Laravel 的 Session 系统实现状态保存：

```php
// Illuminate\Session\Store.php
class Store implements SessionInterface
{
    protected $id;
    protected $name;
    protected $attributes = [];
    protected $handler;
    
    // 保存 Session 状态
    public function save()
    {
        $this->ageFlashData();
        
        $this->handler->write($this->getId(), $this->prepareForStorage(
            serialize($this->attributes)
        ));
        
        $this->started = false;
    }
    
    // 恢复 Session 状态
    public function start()
    {
        $this->loadSession();
        
        if (! $this->has('_token')) {
            $this->regenerateToken();
        }
        
        return $this->started = true;
    }
    
    protected function loadSession()
    {
        $this->attributes = array_merge($this->attributes, $this->readFromHandler());
        
        $this->ageFlashData();
    }
    
    protected function readFromHandler()
    {
        if ($data = $this->handler->read($this->getId())) {
            $data = @unserialize($this->prepareForUnserialize($data));
            
            if ($data !== false && is_array($data)) {
                return $data;
            }
        }
        
        return [];
    }
    
    // 闪存数据（临时备忘录）
    public function flash($key, $value = true)
    {
        $this->put($key, $value);
        $this->push('flash.new', $key);
        
        if (! $this->storingFlash()) {
            $this->ageFlashData();
        }
    }
}

// 使用示例
// 保存状态到 Session
session(['user_preferences' => ['theme' => 'dark', 'language' => 'zh']]);

// 恢复 Session 状态
$preferences = session('user_preferences');

// 使用闪存数据（一次性备忘录）
session()->flash('message', '操作成功！');
```

## 实际应用场景

### 1. 文本编辑器备忘录

实现文本编辑器的撤销/重做功能：

```php
class TextEditor
{
    protected $content = '';
    protected $caretaker;
    
    public function __construct()
    {
        $this->caretaker = new Caretaker($this);
    }
    
    public function write($text)
    {
        $this->caretaker->saveState();
        $this->content .= $text;
    }
    
    public function delete($length)
    {
        $this->caretaker->saveState();
        $this->content = substr($this->content, 0, -$length);
    }
    
    public function getContent()
    {
        return $this->content;
    }
    
    public function setContent($content)
    {
        $this->content = $content;
    }
    
    public function undo()
    {
        $this->caretaker->undo();
    }
    
    public function redo()
    {
        $this->caretaker->redo();
    }
    
    // 创建备忘录
    public function createMemento()
    {
        return new TextMemento($this->content);
    }
    
    // 恢复备忘录
    public function restoreMemento(TextMemento $memento)
    {
        $this->content = $memento->getState();
    }
}

class TextMemento
{
    protected $state;
    protected $timestamp;
    
    public function __construct($state)
    {
        $this->state = $state;
        $this->timestamp = time();
    }
    
    public function getState()
    {
        return $this->state;
    }
    
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}

class Caretaker
{
    protected $originator;
    protected $history = [];
    protected $redoStack = [];
    protected $maxHistory = 100;
    
    public function __construct(TextEditor $originator)
    {
        $this->originator = $originator;
    }
    
    public function saveState()
    {
        // 保存当前状态到历史记录
        $memento = $this->originator->createMemento();
        $this->history[] = $memento;
        
        // 限制历史记录大小
        if (count($this->history) > $this->maxHistory) {
            array_shift($this->history);
        }
        
        // 清空重做栈
        $this->redoStack = [];
    }
    
    public function undo()
    {
        if (empty($this->history)) {
            return false;
        }
        
        // 保存当前状态到重做栈
        $currentMemento = $this->originator->createMemento();
        array_push($this->redoStack, $currentMemento);
        
        // 恢复上一个状态
        $memento = array_pop($this->history);
        $this->originator->restoreMemento($memento);
        
        return true;
    }
    
    public function redo()
    {
        if (empty($this->redoStack)) {
            return false;
        }
        
        // 保存当前状态到历史记录
        $currentMemento = $this->originator->createMemento();
        array_push($this->history, $currentMemento);
        
        // 恢复重做状态
        $memento = array_pop($this->redoStack);
        $this->originator->restoreMemento($memento);
        
        return true;
    }
    
    public function getHistory()
    {
        return $this->history;
    }
}

// 使用示例
$editor = new TextEditor();

$editor->write('Hello');
$editor->write(' World');
echo $editor->getContent(); // 输出: Hello World

$editor->undo();
echo $editor->getContent(); // 输出: Hello

$editor->redo();
echo $editor->getContent(); // 输出: Hello World
```

### 2. 配置管理器备忘录

实现配置管理的撤销功能：

```php
class ConfigurationManager
{
    protected $config = [];
    protected $caretaker;
    
    public function __construct()
    {
        $this->caretaker = new ConfigCaretaker($this);
        $this->loadDefaultConfig();
    }
    
    public function set($key, $value)
    {
        $this->caretaker->saveState();
        $this->config[$key] = $value;
    }
    
    public function get($key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }
    
    public function remove($key)
    {
        $this->caretaker->saveState();
        unset($this->config[$key]);
    }
    
    public function undo()
    {
        return $this->caretaker->undo();
    }
    
    public function redo()
    {
        return $this->caretaker->redo();
    }
    
    public function createMemento()
    {
        return new ConfigMemento($this->config);
    }
    
    public function restoreMemento(ConfigMemento $memento)
    {
        $this->config = $memento->getState();
    }
    
    protected function loadDefaultConfig()
    {
        $this->config = [
            'theme' => 'light',
            'language' => 'en',
            'timezone' => 'UTC'
        ];
    }
}

class ConfigMemento
{
    protected $state;
    
    public function __construct(array $state)
    {
        $this->state = $state;
    }
    
    public function getState()
    {
        return $this->state;
    }
}

class ConfigCaretaker
{
    protected $originator;
    protected $history = [];
    protected $redoStack = [];
    
    public function __construct(ConfigurationManager $originator)
    {
        $this->originator = $originator;
        // 保存初始状态
        $this->saveState();
    }
    
    public function saveState()
    {
        $memento = $this->originator->createMemento();
        $this->history[] = $memento;
        $this->redoStack = []; // 清空重做栈
    }
    
    public function undo()
    {
        if (count($this->history) <= 1) {
            return false; // 不能撤销初始状态
        }
        
        // 保存当前状态到重做栈
        $current = array_pop($this->history);
        array_unshift($this->redoStack, $current);
        
        // 恢复上一个状态
        $previous = end($this->history);
        $this->originator->restoreMemento($previous);
        
        return true;
    }
    
    public function redo()
    {
        if (empty($this->redoStack)) {
            return false;
        }
        
        // 获取重做状态
        $redoState = array_shift($this->redoStack);
        $this->history[] = $redoState;
        $this->originator->restoreMemento($redoState);
        
        return true;
    }
}

// 使用示例
$config = new ConfigurationManager();

echo $config->get('theme'); // 输出: light

$config->set('theme', 'dark');
echo $config->get('theme'); // 输出: dark

$config->undo();
echo $config->get('theme'); // 输出: light

$config->redo();
echo $config->get('theme'); // 输出: dark
```

### 3. 游戏状态备忘录

游戏进度的保存和加载：

```php
class GameState
{
    protected $level;
    protected $score;
    protected $playerHealth;
    protected $inventory = [];
    
    public function __construct($level = 1, $score = 0, $health = 100)
    {
        $this->level = $level;
        $this->score = $score;
        $this->playerHealth = $health;
    }
    
    public function progressToNextLevel()
    {
        $this->level++;
        $this->score += 1000;
    }
    
    public function addScore($points)
    {
        $this->score += $points;
    }
    
    public function takeDamage($damage)
    {
        $this->playerHealth = max(0, $this->playerHealth - $damage);
    }
    
    public function addToInventory($item)
    {
        $this->inventory[] = $item;
    }
    
    public function getState()
    {
        return [
            'level' => $this->level,
            'score' => $this->score,
            'health' => $this->playerHealth,
            'inventory' => $this->inventory,
            'timestamp' => time()
        ];
    }
    
    public function restoreState(array $state)
    {
        $this->level = $state['level'];
        $this->score = $state['score'];
        $this->playerHealth = $state['health'];
        $this->inventory = $state['inventory'];
    }
    
    public function createMemento()
    {
        return new GameMemento($this->getState());
    }
    
    public function restoreMemento(GameMemento $memento)
    {
        $this->restoreState($memento->getState());
    }
}

class GameMemento
{
    protected $state;
    
    public function __construct(array $state)
    {
        $this->state = $state;
    }
    
    public function getState()
    {
        return $this->state;
    }
}

class GameSaveManager
{
    protected $saves = [];
    protected $saveDirectory;
    
    public function __construct($saveDirectory = 'saves')
    {
        $this->saveDirectory = $saveDirectory;
        
        if (!file_exists($this->saveDirectory)) {
            mkdir($this->saveDirectory, 0755, true);
        }
    }
    
    public function saveGame(GameState $game, $saveName)
    {
        $memento = $game->createMemento();
        $filename = $this->saveDirectory . '/' . $saveName . '.save';
        
        file_put_contents($filename, serialize($memento));
        $this->saves[$saveName] = $filename;
    }
    
    public function loadGame($saveName)
    {
        if (!isset($this->saves[$saveName])) {
            throw new InvalidArgumentException("Save file {$saveName} not found");
        }
        
        $filename = $this->saves[$saveName];
        $data = file_get_contents($filename);
        $memento = unserialize($data);
        
        $game = new GameState();
        $game->restoreMemento($memento);
        
        return $game;
    }
    
    public function getSaveList()
    {
        return array_keys($this->saves);
    }
}

// 使用示例
$game = new GameState();
$game->progressToNextLevel();
$game->addScore(500);

$saveManager = new GameSaveManager();
$saveManager->saveGame($game, 'autosave');

// 稍后加载游戏
$loadedGame = $saveManager->loadGame('autosave');
echo $loadedGame->getState()['level']; // 输出: 2
```

## 源码分析要点

### 1. 备忘录接口设计

备忘录模式的核心是定义状态保存和恢复的接口：

```php
interface Memento
{
    public function getState();
    public function getTimestamp();
}

interface Originator
{
    public function createMemento(): Memento;
    public function restoreMemento(Memento $memento);
}

interface Caretaker
{
    public function saveState();
    public function undo();
    public function redo();
}
```

### 2. 状态序列化策略

备忘录模式需要考虑状态序列化策略：

```php
class SerializableMemento implements Memento, Serializable
{
    protected $state;
    
    public function serialize()
    {
        return serialize($this->state);
    }
    
    public function unserialize($data)
    {
        $this->state = unserialize($data);
    }
    
    public function getState()
    {
        return $this->state;
    }
}

class JsonMemento implements Memento, JsonSerializable
{
    protected $state;
    
    public function jsonSerialize()
    {
        return $this->state;
    }
    
    public function getState()
    {
        return $this->state;
    }
}
```

### 3. 状态变更检测

实现高效的状态变更检测：

```php
trait StateChangeTracker
{
    protected $originalState = [];
    protected $currentState = [];
    
    public function trackStateChange($key, $value)
    {
        if (!array_key_exists($key, $this->originalState)) {
            $this->originalState[$key] = $value;
        }
        
        $this->currentState[$key] = $value;
    }
    
    public function isStateChanged()
    {
        return $this->originalState !== $this->currentState;
    }
    
    public function getChangedState()
    {
        $changed = [];
        
        foreach ($this->currentState as $key => $value) {
            if (!array_key_exists($key, $this->originalState) || 
                $this->originalState[$key] !== $value) {
                $changed[$key] = $value;
            }
        }
        
        return $changed;
    }
}
```

## 最佳实践

### 1. 合理使用备忘录模式

**适用场景：**
- 需要实现撤销/重做功能
- 需要保存对象状态快照
- 需要实现检查点功能
- 需要支持状态回滚

**不适用场景：**
- 对象状态过于庞大，内存消耗大
- 状态变更频率过高，性能影响大
- 简单的状态管理需求

### 2. Laravel 中的备忘录实践

**使用数据库事务实现状态回滚：**
```php
// 复杂业务操作的状态保存
DB::beginTransaction();

try {
    // 保存用户
    $user = User::create($userData);
    
    // 保存用户配置
    UserProfile::create(['user_id' => $user->id, 'preferences' => $preferences]);
    
    // 发送欢迎邮件
    Mail::to($user->email)->send(new WelcomeEmail($user));
    
    // 提交事务（确认所有操作成功）
    DB::commit();
    
} catch (Exception $e) {
    // 回滚事务（恢复原始状态）
    DB::rollBack();
    Log::error('用户创建失败: ' . $e->getMessage());
}
```

**使用模型属性跟踪实现撤销功能：**
```php
class Article extends Model
{
    public function saveWithHistory()
    {
        // 保存原始状态
        $original = $this->getOriginal();
        
        if ($this->save()) {
            // 记录变更历史
            ArticleHistory::create([
                'article_id' => $this->id,
                'changes' => $this->getChanges(),
                'original' => $original,
                'user_id' => auth()->id()
            ]);
            
            return true;
        }
        
        return false;
    }
    
    public function restoreVersion($versionId)
    {
        $history = ArticleHistory::find($versionId);
        
        if ($history && $history->article_id === $this->id) {
            $this->fill($history->original);
            return $this->save();
        }
        
        return false;
    }
}

// 使用示例
$article = Article::find(1);
$article->title = '新标题';
$article->saveWithHistory();

// 恢复到历史版本
$article->restoreVersion($versionId);
```

**使用 Session 实现表单数据保存：**
```php
class FormController extends Controller
{
    public function store(Request $request)
    {
        // 保存表单数据到 Session（备忘录）
        $request->session()->put('form_data', $request->all());
        
        try {
            // 处理表单提交
            $result = $this->processForm($request);
            
            // 清除保存的表单数据
            $request->session()->forget('form_data');
            
            return redirect()->route('success');
            
        } catch (Exception $e) {
            // 发生错误时，保留表单数据供用户修改
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

### 3. 测试备忘录模式

**测试状态保存和恢复：**
```php
public function test_memento_saves_and_restores_state()
{
    $editor = new TextEditor();
    $editor->write('Hello');
    
    $memento = $editor->createMemento();
    $editor->write(' World');
    
    $this->assertEquals('Hello World', $editor->getContent());
    
    $editor->restoreMemento($memento);
    $this->assertEquals('Hello', $editor->getContent());
}

public function test_caretaker_manages_history()
{
    $editor = new TextEditor();
    $caretaker = new Caretaker($editor);
    
    $editor->write('First');
    $caretaker->saveState();
    
    $editor->write(' Second');
    $caretaker->saveState();
    
    $caretaker->undo();
    $this->assertEquals('First', $editor->getContent());
    
    $caretaker->redo();
    $this->assertEquals('First Second', $editor->getContent());
}
```

**测试性能优化：**
```php
public function test_memento_performance_with_large_state()
{
    $start = microtime(true);
    
    $largeData = array_fill(0, 10000, 'test data');
    $memento = new LargeDataMemento($largeData);
    
    $serialized = serialize($memento);
    $unserialized = unserialize($serialized);
    
    $end = microtime(true);
    
    $this->assertLessThan(1, $end - $start); // 应该在1秒内完成
    $this->assertEquals($largeData, $unserialized->getState());
}
```

## 与其他模式的关系

### 1. 与命令模式

备忘录模式常与命令模式结合实现撤销功能：

```php
class CommandWithUndo implements Command
{
    protected $receiver;
    protected $memento;
    
    public function __construct(Receiver $receiver)
    {
        $this->receiver = $receiver;
    }
    
    public function execute()
    {
        $this->memento = $this->receiver->createMemento();
        $this->receiver->action();
    }
    
    public function undo()
    {
        $this->receiver->restoreMemento($this->memento);
    }
}
```

### 2. 与原型模式

备忘录模式可以使用原型模式创建状态副本：

```php
class PrototypeMemento implements Memento
{
    protected $state;
    
    public function __construct(Prototype $prototype)
    {
        $this->state = clone $prototype;
    }
    
    public function getState()
    {
        return clone $this->state;
    }
}
```

### 3. 与迭代器模式

备忘录模式可以与迭代器模式结合遍历状态历史：

```php
class HistoryIterator implements Iterator
{
    protected $history;
    protected $position = 0;
    
    public function __construct(array $history)
    {
        $this->history = $history;
    }
    
    public function current()
    {
        return $this->history[$this->position];
    }
    
    public function next()
    {
        $this->position++;
    }
    
    public function rewind()
    {
        $this->position = 0;
    }
    
    public function valid()
    {
        return isset($this->history[$this->position]);
    }
}
```

## 性能考虑

### 1. 状态序列化优化

对于大型状态对象，优化序列化性能：

```php
class OptimizedMemento implements Memento
{
    protected $compressedState;
    
    public function __construct($state)
    {
        // 使用压缩减少存储空间
        $this->compressedState = gzcompress(serialize($state));
    }
    
    public function getState()
    {
        return unserialize(gzuncompress($this->compressedState));
    }
}
```

### 2. 增量状态保存

只保存状态变更部分：

```php
class IncrementalMemento implements Memento
{
    protected $baseState;
    protected $changes;
    
    public function __construct($baseState, $changes)
    {
        $this->baseState = $baseState;
        $this->changes = $changes;
    }
    
    public function getState()
    {
        return array_merge($this->baseState, $this->changes);
    }
    
    public function getChanges()
    {
        return $this->changes;
    }
}
```

### 3. 懒加载状态

对于大型状态，实现懒加载：

```php
class LazyMemento implements Memento
{
    protected $filename;
    protected $state;
    
    public function __construct($state, $filename = null)
    {
        if ($filename) {
            $this->filename = $filename;
            file_put_contents($filename, serialize($state));
        } else {
            $this->state = $state;
        }
    }
    
    public function getState()
    {
        if ($this->state === null && $this->filename) {
            $this->state = unserialize(file_get_contents($this->filename));
        }
        
        return $this->state;
    }
}
```

## 总结

备忘录模式在 Laravel 框架中有着重要的应用，特别是在数据库事务、模型属性跟踪和 Session 管理中。它通过保存对象状态快照，实现了状态回滚、撤销操作和历史记录等功能。

备忘录模式的优势在于：
- **状态保护**：不破坏对象的封装性
- **灵活恢复**：支持多种状态恢复策略
- **历史管理**：维护完整的状态历史记录
- **错误恢复**：提供可靠的错误恢复机制

在 Laravel 开发中，合理使用备忘录模式可以创建出健壮、可靠的应用程序，特别是在需要实现撤销功能、状态回滚或检查点功能的业务场景中。