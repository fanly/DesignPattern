# Command Pattern

## Overview

Encapsulate a request as an object, thereby letting you parameterize clients with different requests, queue or log requests, and support undoable operations.

## Architecture Diagram

### Command Pattern Structure

```mermaid
classDiagram
    class Command {
        <<interface>>
        +execute(): void
        +undo(): void
    }
    
    class ConcreteCommand {
        -receiver: Receiver
        -state: State
        +execute(): void
        +undo(): void
    }
    
    class Receiver {
        +action(): void
    }
    
    class Invoker {
        -command: Command
        +setCommand(command: Command): void
        +executeCommand(): void
    }
    
    class Client {
        +main(): void
    }
    
    Command <|.. ConcreteCommand : implements
    ConcreteCommand --> Receiver : calls
    Invoker --> Command : executes
    Client --> ConcreteCommand : creates
    Client --> Invoker : configures
```

### Laravel Job Queue System

```mermaid
graph TB
    A[Dispatch Job] --> B[Queue Manager]
    B --> C[Queue Driver]
    C --> D[Job Storage]
    D --> E[Queue Worker]
    E --> F[Job Execution]
    F --> G[Job Handler]
    
    style A fill:#e1f5fe
    style B fill:#f3e5f5
    style F fill:#fff3e0
```

### Command Execution Flow

```mermaid
sequenceDiagram
    participant Client
    participant Dispatcher
    participant Queue
    participant Worker
    participant JobHandler
    
    Client->>Dispatcher: dispatch(SendEmailJob)
    Dispatcher->>Queue: push(job)
    Queue-->>Dispatcher: job queued
    Dispatcher-->>Client: job dispatched
    
    Worker->>Queue: pop()
    Queue-->>Worker: SendEmailJob
    Worker->>JobHandler: handle()
    JobHandler->>JobHandler: send email
    JobHandler-->>Worker: completed
    Worker->>Queue: markAsProcessed()
    
    note over JobHandler: Command executes business logic
```

## Implementation in Laravel

### 1. Job Commands

Laravel's job system implements the Command pattern:

```php
// Job interface (Command)
interface ShouldQueue
{
    public function handle();
}

// Concrete command
class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    protected $user;
    protected $message;
    
    public function __construct(User $user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }
    
    public function handle(MailManager $mailer)
    {
        $mailer->to($this->user->email)->send(new WelcomeEmail($this->message));
    }
}

// Invoker
class Dispatcher
{
    public function dispatch($command)
    {
        if ($this->queueResolver && $this->commandShouldBeQueued($command)) {
            return $this->dispatchToQueue($command);
        }
        
        return $this->dispatchNow($command);
    }
}
```

### 2. Artisan Commands

```php
// Artisan command as Command pattern
class MakeControllerCommand extends GeneratorCommand
{
    protected $signature = 'make:controller {name : The name of the controller}';
    protected $description = 'Create a new controller class';
    
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $path = $this->getPath($name);
        
        if ($this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');
            return false;
        }
        
        $this->makeDirectory($path);
        $this->files->put($path, $this->buildClass($name));
        $this->info($this->type.' created successfully.');
    }
}
```