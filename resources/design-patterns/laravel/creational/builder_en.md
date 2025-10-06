# Builder Pattern

## Overview

Separate the construction of a complex object from its representation so that the same construction process can create different representations. The Builder pattern allows you to construct complex objects step by step.

## Problem Scenarios

In Laravel applications, we often need:
- Create complex configuration objects
- Build complex query statements
- Create multi-step forms
- Build complex email content
- Create multi-level menu structures

## Solution

The Builder pattern provides a builder class to gradually build complex objects, separating the construction process from the final product.

## Architecture Diagrams

### Builder Pattern Structure

```mermaid
classDiagram
    class Director {
        -builder
        +construct()
    }
    
    class Builder {
        +buildPartA()
        +buildPartB()
        +getResult()
    }
    
    class ConcreteBuilder {
        -product
        +buildPartA()
        +buildPartB()
        +getResult()
    }
    
    class Product {
        -partA
        -partB
        +setPartA()
        +setPartB()
    }
    
    Director --> Builder
    Builder <|-- ConcreteBuilder
    ConcreteBuilder --> Product
```

### Laravel Query Builder Architecture

```mermaid
graph TB
    A[Query Builder] --> B[Select Builder]
    A --> C[Where Builder]
    A --> D[Join Builder]
    A --> E[Order Builder]
    A --> F[Group Builder]
    
    B --> G[SQL Query]
    C --> G
    D --> G
    E --> G
    F --> G
    
    G --> H[Execute Query]
    H --> I[Result Set]
    
    style A fill:#e1f5fe
    style G fill:#f3e5f5
    style I fill:#fff3e0
```

### Builder Construction Flow

```mermaid
sequenceDiagram
    participant Client
    participant QueryBuilder
    participant WhereBuilder
    participant JoinBuilder
    participant SQL
    
    Client->>QueryBuilder: select('*')
    QueryBuilder->>QueryBuilder: addSelect('*')
    
    Client->>QueryBuilder: where('status', 'active')
    QueryBuilder->>WhereBuilder: addWhere('status', 'active')
    
    Client->>QueryBuilder: join('users', 'posts.user_id', 'users.id')
    QueryBuilder->>JoinBuilder: addJoin('users', ...)
    
    Client->>QueryBuilder: get()
    QueryBuilder->>SQL: toSql()
    SQL-->>QueryBuilder: compiled SQL
    QueryBuilder->>QueryBuilder: execute()
    QueryBuilder-->>Client: Collection
    
    note over QueryBuilder: Builds query step by step
```

## Laravel Implementation

### 1. Query Builder Example

```php
<?php

namespace App\Patterns\Builder;

// Query product class
class Query
{
    private array $select = [];
    private string $from = '';
    private array $joins = [];
    private array $where = [];
    private array $orderBy = [];
    private array $groupBy = [];
    private ?int $limit = null;
    private ?int $offset = null;
    
    public function setSelect(array $select): void
    {
        $this->select = $select;
    }
    
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }
    
    public function addJoin(string $join): void
    {
        $this->joins[] = $join;
    }
    
    public function addWhere(string $where): void
    {
        $this->where[] = $where;
    }
    
    public function addOrderBy(string $orderBy): void
    {
        $this->orderBy[] = $orderBy;
    }
    
    public function addGroupBy(string $groupBy): void
    {
        $this->groupBy[] = $groupBy;
    }
    
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }
    
    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }
    
    public function toSql(): string
    {
        $sql = 'SELECT ' . implode(', ', $this->select);
        $sql .= " FROM {$this->from}";
        
        foreach ($this->joins as $join) {
            $sql .= " {$join}";
        }
        
        if (!empty($this->where)) {
            $sql .= ' WHERE ' . implode(' AND ', $this->where);
        }
        
        if (!empty($this->groupBy)) {
            $sql .= ' GROUP BY ' . implode(', ', $this->groupBy);
        }
        
        if (!empty($this->orderBy)) {
            $sql .= ' ORDER BY ' . implode(', ', $this->orderBy);
        }
        
        if ($this->limit !== null) {
            $sql .= " LIMIT {$this->limit}";
        }
        
        if ($this->offset !== null) {
            $sql .= " OFFSET {$this->offset}";
        }
        
        return $sql;
    }
}

// Abstract query builder
abstract class QueryBuilder
{
    protected Query $query;
    
    public function __construct()
    {
        $this->reset();
    }
    
    public function reset(): void
    {
        $this->query = new Query();
    }
    
    abstract public function select(array $columns): self;
    abstract public function from(string $table): self;
    abstract public function where(string $column, string $operator, $value): self;
    abstract public function join(string $table, string $on): self;
    abstract public function orderBy(string $column, string $direction = 'ASC'): self;
    abstract public function limit(int $limit): self;
    abstract public function offset(int $offset): self;
    
    public function getQuery(): Query
    {
        $result = $this->query;
        $this->reset();
        return $result;
    }
}

// MySQL query builder
class MysqlQueryBuilder extends QueryBuilder
{
    public function select(array $columns): self
    {
        $this->query->setSelect($columns);
        return $this;
    }
    
    public function from(string $table): self
    {
        $this->query->setFrom("`{$table}`");
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self
    {
        $value = is_string($value) ? "'{$value}'" : $value;
        $this->query->addWhere("`{$column}` {$operator} {$value}");
        return $this;
    }
    
    public function join(string $table, string $on): self
    {
        $this->query->addJoin("JOIN `{$table}` ON {$on}");
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->query->addOrderBy("`{$column}` {$direction}");
        return $this;
    }
    
    public function limit(int $limit): self
    {
        $this->query->setLimit($limit);
        return $this;
    }
    
    public function offset(int $offset): self
    {
        $this->query->setOffset($offset);
        return $this;
    }
}

// PostgreSQL query builder
class PostgresqlQueryBuilder extends QueryBuilder
{
    public function select(array $columns): self
    {
        $this->query->setSelect($columns);
        return $this;
    }
    
    public function from(string $table): self
    {
        $this->query->setFrom("\"{$table}\"");
        return $this;
    }
    
    public function where(string $column, string $operator, $value): self
    {
        $value = is_string($value) ? "'{$value}'" : $value;
        $this->query->addWhere("\"{$column}\" {$operator} {$value}");
        return $this;
    }
    
    public function join(string $table, string $on): self
    {
        $this->query->addJoin("JOIN \"{$table}\" ON {$on}");
        return $this;
    }
    
    public function orderBy(string $column, string $direction = 'ASC'): self
    {
        $this->query->addOrderBy("\"{$column}\" {$direction}");
        return $this;
    }
    
    public function limit(int $limit): self
    {
        $this->query->setLimit($limit);
        return $this;
    }
    
    public function offset(int $offset): self
    {
        $this->query->setOffset($offset);
        return $this;
    }
}
```

### 2. Email Builder Example

```php
<?php

namespace App\Patterns\Builder;

// Email product class
class Email
{
    private string $to = '';
    private string $from = '';
    private string $subject = '';
    private string $body = '';
    private array $attachments = [];
    private array $headers = [];
    private bool $isHtml = false;
    
    public function setTo(string $to): void
    {
        $this->to = $to;
    }
    
    public function setFrom(string $from): void
    {
        $this->from = $from;
    }
    
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }
    
    public function setBody(string $body): void
    {
        $this->body = $body;
    }
    
    public function addAttachment(string $attachment): void
    {
        $this->attachments[] = $attachment;
    }
    
    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
    
    public function setIsHtml(bool $isHtml): void
    {
        $this->isHtml = $isHtml;
    }
    
    public function send(): string
    {
        $message = "Sending email:\n";
        $message .= "To: {$this->to}\n";
        $message .= "From: {$this->from}\n";
        $message .= "Subject: {$this->subject}\n";
        $message .= "Body: {$this->body}\n";
        $message .= "HTML: " . ($this->isHtml ? 'Yes' : 'No') . "\n";
        
        if (!empty($this->attachments)) {
            $message .= "Attachments: " . implode(', ', $this->attachments) . "\n";
        }
        
        if (!empty($this->headers)) {
            $message .= "Headers: " . json_encode($this->headers) . "\n";
        }
        
        return $message;
    }
}

// Email builder interface
interface EmailBuilderInterface
{
    public function to(string $email): self;
    public function from(string $email): self;
    public function subject(string $subject): self;
    public function body(string $body): self;
    public function attachment(string $file): self;
    public function header(string $name, string $value): self;
    public function html(bool $isHtml = true): self;
    public function build(): Email;
}

// Concrete email builder
class EmailBuilder implements EmailBuilderInterface
{
    private Email $email;
    
    public function __construct()
    {
        $this->reset();
    }
    
    public function reset(): void
    {
        $this->email = new Email();
    }
    
    public function to(string $email): self
    {
        $this->email->setTo($email);
        return $this;
    }
    
    public function from(string $email): self
    {
        $this->email->setFrom($email);
        return $this;
    }
    
    public function subject(string $subject): self
    {
        $this->email->setSubject($subject);
        return $this;
    }
    
    public function body(string $body): self
    {
        $this->email->setBody($body);
        return $this;
    }
    
    public function attachment(string $file): self
    {
        $this->email->addAttachment($file);
        return $this;
    }
    
    public function header(string $name, string $value): self
    {
        $this->email->addHeader($name, $value);
        return $this;
    }
    
    public function html(bool $isHtml = true): self
    {
        $this->email->setIsHtml($isHtml);
        return $this;
    }
    
    public function build(): Email
    {
        $result = $this->email;
        $this->reset();
        return $result;
    }
}

// Email director
class EmailDirector
{
    private EmailBuilderInterface $builder;
    
    public function __construct(EmailBuilderInterface $builder)
    {
        $this->builder = $builder;
    }
    
    public function buildWelcomeEmail(string $to, string $name): Email
    {
        return $this->builder
            ->to($to)
            ->from('noreply@example.com')
            ->subject('Welcome to our service!')
            ->body("<h1>Welcome {$name}!</h1><p>Thank you for registering for our service.</p>")
            ->html(true)
            ->header('X-Priority', '1')
            ->build();
    }
    
    public function buildPasswordResetEmail(string $to, string $token): Email
    {
        return $this->builder
            ->to($to)
            ->from('security@example.com')
            ->subject('Password Reset Request')
            ->body("Your password reset token is: {$token}")
            ->html(false)
            ->header('X-Priority', '1')
            ->build();
    }
    
    public function buildInvoiceEmail(string $to, string $invoicePath): Email
    {
        return $this->builder
            ->to($to)
            ->from('billing@example.com')
            ->subject('Your Invoice')
            ->body('<p>Please find your invoice attached.</p>')
            ->html(true)
            ->attachment($invoicePath)
            ->build();
    }
}
```

### 3. Form Builder Example

```php
<?php

namespace App\Patterns\Builder;

// Form element base class
abstract class FormElement
{
    protected string $name;
    protected string $label;
    protected array $attributes = [];
    
    public function __construct(string $name, string $label = '')
    {
        $this->name = $name;
        $this->label = $label;
    }
    
    public function setAttribute(string $name, string $value): self
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    
    abstract public function render(): string;
}

// Input element
class InputElement extends FormElement
{
    private string $type = 'text';
    private string $value = '';
    
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }
    
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }
    
    public function render(): string
    {
        $attrs = '';
        foreach ($this->attributes as $name => $value) {
            $attrs .= " {$name}=\"{$value}\"";
        }
        
        $html = '';
        if ($this->label) {
            $html .= "<label for=\"{$this->name}\">{$this->label}</label>";
        }
        $html .= "<input type=\"{$this->type}\" name=\"{$this->name}\" id=\"{$this->name}\" value=\"{$this->value}\"{$attrs}>";
        
        return $html;
    }
}

// Select element
class SelectElement extends FormElement
{
    private array $options = [];
    private string $selected = '';
    
    public function addOption(string $value, string $text): self
    {
        $this->options[$value] = $text;
        return $this;
    }
    
    public function setSelected(string $selected): self
    {
        $this->selected = $selected;
        return $this;
    }
    
    public function render(): string
    {
        $attrs = '';
        foreach ($this->attributes as $name => $value) {
            $attrs .= " {$name}=\"{$value}\"";
        }
        
        $html = '';
        if ($this->label) {
            $html .= "<label for=\"{$this->name}\">{$this->label}</label>";
        }
        $html .= "<select name=\"{$this->name}\" id=\"{$this->name}\"{$attrs}>";
        
        foreach ($this->options as $value => $text) {
            $selected = $value === $this->selected ? ' selected' : '';
            $html .= "<option value=\"{$value}\"{$selected}>{$text}</option>";
        }
        
        $html .= "</select>";
        
        return $html;
    }
}

// Form product class
class Form
{
    private string $action = '';
    private string $method = 'POST';
    private array $elements = [];
    private array $attributes = [];
    
    public function setAction(string $action): void
    {
        $this->action = $action;
    }
    
    public function setMethod(string $method): void
    {
        $this->method = $method;
    }
    
    public function addElement(FormElement $element): void
    {
        $this->elements[] = $element;
    }
    
    public function setAttribute(string $name, string $value): void
    {
        $this->attributes[$name] = $value;
    }
    
    public function render(): string
    {
        $attrs = '';
        foreach ($this->attributes as $name => $value) {
            $attrs .= " {$name}=\"{$value}\"";
        }
        
        $html = "<form action=\"{$this->action}\" method=\"{$this->method}\"{$attrs}>";
        
        foreach ($this->elements as $element) {
            $html .= "<div class=\"form-group\">";
            $html .= $element->render();
            $html .= "</div>";
        }
        
        $html .= "</form>";
        
        return $html;
    }
}

// Form builder
class FormBuilder
{
    private Form $form;
    
    public function __construct()
    {
        $this->reset();
    }
    
    public function reset(): void
    {
        $this->form = new Form();
    }
    
    public function action(string $action): self
    {
        $this->form->setAction($action);
        return $this;
    }
    
    public function method(string $method): self
    {
        $this->form->setMethod($method);
        return $this;
    }
    
    public function attribute(string $name, string $value): self
    {
        $this->form->setAttribute($name, $value);
        return $this;
    }
    
    public function input(string $name, string $label = '', string $type = 'text'): self
    {
        $element = new InputElement($name, $label);
        $element->setType($type);
        $this->form->addElement($element);
        return $this;
    }
    
    public function select(string $name, string $label = '', array $options = []): self
    {
        $element = new SelectElement($name, $label);
        foreach ($options as $value => $text) {
            $element->addOption($value, $text);
        }
        $this->form->addElement($element);
        return $this;
    }
    
    public function build(): Form
    {
        $result = $this->form;
        $this->reset();
        return $result;
    }
}
```

## Usage Examples

### Query Builder Usage

```php
<?php

// MySQL query building
$mysqlBuilder = new MysqlQueryBuilder();
$query = $mysqlBuilder
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('status', '=', 'active')
    ->where('age', '>', 18)
    ->join('profiles', 'users.id = profiles.user_id')
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->getQuery();

echo "MySQL Query: " . $query->toSql() . "\n";

// PostgreSQL query building
$pgsqlBuilder = new PostgresqlQueryBuilder();
$query = $pgsqlBuilder
    ->select(['id', 'name', 'email'])
    ->from('users')
    ->where('status', '=', 'active')
    ->orderBy('name', 'ASC')
    ->limit(5)
    ->getQuery();

echo "PostgreSQL Query: " . $query->toSql() . "\n";
```

### Email Builder Usage

```php
<?php

$emailBuilder = new EmailBuilder();
$director = new EmailDirector($emailBuilder);

// Build welcome email
$welcomeEmail = $director->buildWelcomeEmail('user@example.com', 'John Doe');
echo $welcomeEmail->send();

// Build password reset email
$resetEmail = $director->buildPasswordResetEmail('user@example.com', 'abc123');
echo $resetEmail->send();

// Build invoice email
$invoiceEmail = $director->buildInvoiceEmail('user@example.com', '/path/to/invoice.pdf');
echo $invoiceEmail->send();

// Custom email building
$customEmail = $emailBuilder
    ->to('admin@example.com')
    ->from('system@example.com')
    ->subject('System Report')
    ->body('<h2>System Running Normally</h2><p>All services are running normally.</p>')
    ->html(true)
    ->header('X-Priority', '3')
    ->build();

echo $customEmail->send();
```

### Form Builder Usage

```php
<?php

$formBuilder = new FormBuilder();

// Build login form
$loginForm = $formBuilder
    ->action('/login')
    ->method('POST')
    ->attribute('class', 'login-form')
    ->input('username', 'Username', 'text')
    ->input('password', 'Password', 'password')
    ->build();

echo $loginForm->render();

// Build registration form
$registerForm = $formBuilder
    ->action('/register')
    ->method('POST')
    ->input('name', 'Full Name', 'text')
    ->input('email', 'Email', 'email')
    ->input('password', 'Password', 'password')
    ->select('gender', 'Gender', ['male' => 'Male', 'female' => 'Female'])
    ->build();

echo $registerForm->render();
```

## Practical Applications in Laravel

### 1. Eloquent Query Builder

```php
<?php

// Laravel's query builder is a typical application of the Builder pattern
$users = DB::table('users')
    ->select('name', 'email')
    ->where('active', 1)
    ->where('age', '>', 18)
    ->orderBy('created_at', 'desc')
    ->limit(10)
    ->get();

// Eloquent model queries
$posts = Post::where('published', true)
    ->with('author')
    ->orderBy('created_at', 'desc')
    ->paginate(15);
```

### 2. Route Builder

```php
<?php

// Laravel's route definition also uses the Builder pattern
Route::get('/users/{id}', [UserController::class, 'show'])
    ->name('users.show')
    ->middleware('auth')
    ->where('id', '[0-9]+');

Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {
    Route::resource('posts', PostController::class);
});
```

### 3. Validation Rule Builder

```php
<?php

// Laravel's validation rule building
$validator = Validator::make($request->all(), [
    'name' => 'required|string|max:255',
    'email' => 'required|email|unique:users',
    'password' => 'required|min:8|confirmed',
]);

// Using Rule class to build complex rules
$rules = [
    'email' => [
        'required',
        'email',
        Rule::unique('users')->ignore($user->id),
    ],
];
```

## Sequence Diagram

```mermaid
sequenceDiagram
    participant Client
    participant Director
    participant Builder
    participant Product
    
    Client->>Director: construct()
    Director->>Builder: buildPartA()
    Director->>Builder: buildPartB()
    Director->>Builder: buildPartC()
    Director->>Builder: getResult()
    Builder->>Product: new Product()
    Product-->>Builder: instance
    Builder-->>Director: product
    Director-->>Client: product
```

## Advantages

1. **Separates construction and representation**: Construction process and final product are separated
2. **Fine control over construction process**: Can gradually build complex objects
3. **Code reuse**: Same construction process can create different products
4. **Easy to extend**: Construction process can be extended independently

## Disadvantages

1. **Increased complexity**: Introduces additional builder classes
2. **Similar product structure**: Requires products to have sufficient commonality

## Applicable Scenarios

1. **When creating complex objects**
2. **When construction process must allow different representations**
3. **When objects need to be built step by step**
4. **When construction process requires fine control**

## Relationship with Other Patterns

- **Abstract Factory Pattern**: Builder focuses on step-by-step construction, Abstract Factory focuses on product families
- **Composite Pattern**: Builder is often used to create complex structures of Composite pattern
- **Template Method Pattern**: Builder's construction process can use Template Method

The Builder pattern is widely used in Laravel, especially in scenarios that require step-by-step construction of complex objects.