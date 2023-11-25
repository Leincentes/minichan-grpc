# minichan-v1

A PHP gRPC library utilizing Swoole coroutines, encompassing a protoc code generator, server, and client components.

## OVERVIEW
**minichan-v1** is a PHP gRPC library that leverages Swoole coroutines. It encompasses a protoc code generator, server, and client components to facilitate the development of gRPC-based applications. 
### Table of Contents
- [Installation](#installation)
- [Protoc Code Generation](#generate-php-code-via-proto)
- [Basic gRPC Server](#basic-grpc-server)
- [Basic gRPC Client](#basic-grpc-client)
- [CLI minichan](#cli-minichan)
- [Usage](#usage)
- [Config](#config)
- [Database](#database)
- [Middleware](#middleware)
- [Server Class](#server-class)

# INSTALLATION
Swoole >= 5.1.0: https://wiki.swoole.com/#/environment
It needs to be turned on --enable-http2

[Protoc](https://github.com/protocolbuffers/protobuf) is a code generator for protobuf data structures, tasked with converting .proto files into language-specific classes and structs for program implementation.

[minichan]() is a protoc plug-in created by the grpc library, designed to generate server and client code for services.

## GENERATE PHP CODE via .proto

```proto

syntax = "proto3";

package PHP.UserService;

message User {
  string username = 1;
  string password = 2;
  string new_password = 3;
}
message UserResponse {
    string message = 1;
}

service UserService {
  // Register a new user
  rpc RegisterUser(User) returns (UserResponse);

  // Login with user credentials
  rpc Login(User) returns (UserResponse);

  // Update user information
  rpc UpdateUser(User) returns (UserResponse);

  // Delete a user account
  rpc DeleteUser(User) returns (UserResponse);

  // Fetch a user account
  rpc GetUser(User) returns (UserResponse);

  // Fetch all user account
  rpc GetAllUser(User) returns (stream UserResponse);
}

service Stream {
    rpc FetchRegistered(User) returns (stream UserResponse);
    rpc FetchLogin(User) returns (stream UserResponse);
    rpc FetchDelete(User) returns (stream UserResponse);
    rpc FetchUpdates(User) returns (stream UserResponse);
    rpc FetchUser(User) returns (UserResponse);
    rpc FetchAllUser(User) returns (stream UserResponse);
}

```

Then use protoc to generate the code:
```bash
protoc --php_out=. --grpc_out=. --plugin=protoc-gen-grpc=lib/Bin/minichan proto/userauth.proto
```
When the command is executed, the following files will be generated in the current directory:
```bash
|--GPBMetadata/Proto
| `--Userauth.php
|--PHP/UserService
| `--UserResponse.php
| `--UserServiceClient.php
| `--UserServiceInterface.php
| `--User.php
| `--StreamClient.php
| `--StreamInterface.php
``--userauth.proto

```
Now that the stubs are generated using our binary plugin.

Next, we'll add the generated file to composer autoload, and we'll modify composer.json:
```json

"autoload-dev": {
    "psr-4": {
        "GPBMetadata\\Proto\\": "GPBMetadata/Proto/",
        "PHP\\": "PHP/"
    }
}

```
Modify and execute to make it effective, 
```bash 
composer dump-autoload
```

# Service
## Sample Service

### AuthService Class Creation

The `AuthService` class is a service implementation that provides functionality related to user authentication. It implements the `UserServiceInterface` and offers methods for user registration, login, updating user information, deleting users, retrieving individual users, and retrieving a list of all users.

### Class Structure

#### Dependencies

- `Minichan\Database\Database`: A class representing database interactions.
- `PHP\UserService\User`: The generated PHP class representing a user in the gRPC service.
- `PHP\UserService\UserResponse`: The generated PHP class representing a response in the gRPC service.
- `PHP\UserService\UserServiceInterface`: The generated PHP interface for the gRPC user service.
- `Minichan\Exception\InvokeException`: Custom exception class for invocation errors.
- `Minichan\Exception\AlreadyExistsException`: Custom exception class for already existing entities.
- `Minichan\Grpc\ContextInterface`: Interface for managing values associated with a specific request.

#### Properties

- `$response`: An instance of `UserResponse` used for constructing and returning responses.
- `$db`: An instance of the `Database` class for handling database operations.

### Constructor

#### `__construct()`

**Description:**
Initializes the class by creating a `UserResponse` instance and setting up the database connection.

### Public Methods

#### `RegisterUser(ContextInterface $ctx, User $request): UserResponse`

**Description:**
Registers a new user by checking for existing usernames, hashing the password, and inserting the user record into the database.

**Parameters:**
- `$ctx (ContextInterface)`: The context of the gRPC request.
- `$request (User)`: The user registration request.

**Returns:**
- `UserResponse`: A response indicating the status of the user registration.

**Throws:**
- `\Minichan\Exception\AlreadyExistsException`: If the username already exists in the database.
- `\Minichan\Exception\InvokeException`: If there is an issue with the registration process.

#### `Login(ContextInterface $ctx, User $request): UserResponse`

**Description:**
Validates user credentials for login by retrieving the user from the database and comparing the hashed password.

**Parameters:**
- `$ctx (ContextInterface)`: The context of the gRPC request.
- `$request (User)`: The user login request.

**Returns:**
- `UserResponse`: A response indicating the status of the user login.

**Throws:**
- `\Minichan\Exception\InvokeException`: If authentication fails or the user is not found.

#### `UpdateUser(ContextInterface $ctx, User $request): UserResponse`

**Description:**
Updates user information by updating the user record in the database.

**Parameters:**
- `$ctx (ContextInterface)`: The context of the gRPC request.
- `$request (User)`: The user update request.

**Returns:**
- `UserResponse`: A response indicating the status of the user update.

**Throws:**
- `\Minichan\Exception\InvokeException`: If the update operation fails.

#### `DeleteUser(ContextInterface $ctx, User $request): UserResponse`

**Description:**
Deletes a user by removing the user record from the database.

**Parameters:**
- `$ctx (ContextInterface)`: The context of the gRPC request.
- `$request (User)`: The user deletion request.

**Returns:**
- `UserResponse`: A response indicating the status of the user deletion.

**Throws:**
- `\Minichan\Exception\InvokeException`: If the deletion operation fails.

#### `GetUser(ContextInterface $ctx, User $request): UserResponse`

**Description:**
Retrieves a user by querying the database based on the provided username.

**Parameters:**
- `$ctx (ContextInterface)`: The context of the gRPC request.
- `$request (User)`: The user retrieval request.

**Returns:**
- `UserResponse`: A response containing information about the retrieved user.

**Throws:**
- `\Minichan\Exception\InvokeException`: If the user is not found.

#### `GetAllUser(ContextInterface $ctx, User $request): UserResponse`

**Description:**
Retrieves a list of all users from the database.

**Parameters:**
- `$ctx (ContextInterface)`: The context of the gRPC request.
- `$request (User)`: The request for retrieving all users.

**Returns:**
- `UserResponse`: A response containing information about all users.

**Throws:**
- `\Minichan\Exception\InvokeException`: If there is an issue retrieving the user list.

## Usage
Under the Services folder your going to create the AuthService class that will implement UserServiceInterface that the binary plugin generated.
```php
// Services/AuthService.php
declare(strict_types=1);

namespace Services;

use Minichan\Database\Database;
use PHP\UserService\User;
use PHP\UserService\UserResponse;
use PHP\UserService\UserServiceInterface;

class AuthService implements UserServiceInterface
{
    private UserResponse $response;
    private Database $db;
    public function __construct() {
        $this->response = new UserResponse();
        // dynamic instance that depends on you
        $this->db = new Database([
            'type' => 'mysql',
            'host' => 'localhost',
            'database' => 'db_name',
            'username' => 'db_username',
            'password' => 'db_password'
        ]);
    }
    /**
    * @param \Minichan\Grpc\ContextInterface $ctx
    * @param User $request
    * @return User
    *
    * @throws \Minichan\Exception\InvokeException
    */
    public function RegisterUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $existingUsers = $this->db->select('users', ['username'], ['username' => $request->getUsername()]);

        if (count($existingUsers) > 0) {
            throw new \Minichan\Exception\AlreadyExistsException("user with the provided username already exists");
        }

        $userRecord = [
            'username' => $request->getUsername(),
            'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
        ];

        $this->db->insert('users', $userRecord);

        return $this->response->setMessage('registered user: ' . $userRecord['username']);
    }   

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return UserResponse
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function Login(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $users = $this->db->select('users', ['username', 'password'],
            [
                'username' => $request->getUsername(),
            ]);
        if (count($users) > 0) {
            $user = $users[0];
    
            if (password_verify($request->getPassword(), $user['password'])) {
                return $this->response->setMessage('User login successful');
            } else {
                throw new \Minichan\Exception\InvokeException("authentication failed");
            }
        } else {
            throw new \Minichan\Exception\InvokeException("user not found");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function UpdateUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $users = $this->db->update('users', 
            [
                'username' => $request->getUsername(),
                'password' => password_hash($request->getPassword(), PASSWORD_DEFAULT),
            ], [
            'username' => $request->getUsername(),
            ]);
        if($users) {
            return $this->response->setMessage('user updated successfully');
        } else {
            throw new \Minichan\Exception\InvokeException("update user failed");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function DeleteUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {
        $users = $this->db->delete('users', [
            'username' => $request->getUsername(),
        ]);
        if($users) {
            return $this->response->setMessage('user deleted successfully');
        } else {
            throw new \Minichan\Exception\InvokeException("delete user failed");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function GetUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse {

        $users = $this->db->get('users', ['username', 'password'], ['username' => $request->getUsername()]);

        if($users) {
            return $this->response->setMessage('user: ' . $users['username']);
        } else {
            throw new \Minichan\Exception\InvokeException("user does not exist");
        }
    }

    /**
     * @param \Minichan\Grpc\ContextInterface $ctx
     * @param User $request
     * @return User[]
     *
     * @throws \Minichan\Exception\InvokeException
     */
    public function GetAllUser(\Minichan\Grpc\ContextInterface $ctx, User $request): UserResponse { 
        
        $users = $this->db->select('users', ['username', 'password']);
        $responseMessage = '';

        foreach ($users as $user) {
            $responseMessage .= 'users: ' . $user['username'] . PHP_EOL;
        }
    
        return $this->response->setMessage($responseMessage);
    }
}

```

## Add the Service in the Config
In the folder Services you'll find Cofig folder where Config.php reside. This is where you can register the Service you created.
```php
<?php

declare(strict_types=1);

namespace Services\Config;

use Services\AuthService;

/**
 * Configuration class for defining gRPC services.
 */
class Config
{
    /**
     * Register gRPC services.
     *
     * @return array
     */
    public static function registerServices(): array
    {
        // Add your services here
        return [
            AuthService::class,
        ];
    }
}

```

## Basic gRPC Server
All you needed to do is to execute the file.
```php
// serve.php

declare(strict_types= 1);

use Minichan\Config\Config;
use Minichan\Config\Constant;

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));

require_once BASE_PATH . '/vendor/autoload.php';

// Create a gRPC server instance with the specified host, port, and Swoole mode
$server = (new \Minichan\Grpc\Server(Constant::SERVER_HOST, Constant::SERVER_PORT, SWOOLE_BASE))

    // Register gRPC services defined in the configuration
    ->registerServices(Config::getServices())

    // Define worker context data with a callback to set 'worker_start_time' to the current time
    ->withWorkerContext('worker_start_time', function () {
        return time();
    })

    // Add middlewares defined in the configuration
    ->addMiddlewares(Config::getMiddlewares())

    // Set additional server options
    ->set([
        'worker_num' => 6,                  // Number of worker processes
        'open_http2_protocol' => 1,         // Enable HTTP2 protocol
        'enable_coroutine' => true,         // Enable coroutine support
    ]);

$server->start();

```
## To Serve
```bash
php minichan serve
```

## Basic gRPC Client
A basic example how the client will be handled.
```php
// client.php

declare(strict_types=1);

use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use Swoole\Coroutine as Co;
use PHP\UserService\User;

require __DIR__ . '/vendor/autoload.php';

function connectToGrpcServer()
{
    return (new Client('localhost', 9502, Constant::GRPC_CALL))
        ->set(['open_http2_protocol' => 1])
        ->connect();
}

function sendDataToGrpcServer($conn, $method, $message, $type = 'proto', $user_agent = 'grpc-java-net/sample')
{
    $streamId = $conn->send($method, $message, $type, $user_agent);
    return $conn->recv($streamId);
}

function performGrpcMethod($method, User $message)
{
    $conn = connectToGrpcServer();
    $data = sendDataToGrpcServer($conn, $method, $message);
    $conn->close();
    echo "Closed\n";
    return $data;
}

Co::set(['log_level' => SWOOLE_LOG_ERROR]);
Co::set(['log_level' => SWOOLE_LOG_DEBUG]);

Co::create(function () {
    // uncomment - comment to test all the service available
    $method = '/PHP.UserService.UserService/RegisterUser';
    // $method = '/PHP.UserService.UserService/Login';
    // $method = '/PHP.UserService.UserService/UpdateUser';
    // $method = '/PHP.UserService.UserService/DeleteUser';
    // $method = '/PHP.UserService.UserService/GetUser';
    // $method = '/PHP.UserService.UserService/GetAllUser';
    $message = new User();
    $message->setUsername('Hello');
    $message->setPassword('Hi2');
    // $message->setNewPassword('Hi');

    $data = performGrpcMethod($method, $message);

    print_r($data);
});

```


# CLI minichan
A command-line minichan interface is for generating various classes and interfaces. This tool aims to streamline the process of creating classes and interfaces.

## Usage
General Usage <a name="general-usage"></a>
```bash
./minichan [command] [arguments]
or 
php minichan [command] [arguments]
```

Available Commands <a name="available-commands"></a>
Use the following command to display a list of available commands:
```bash
./minichan help
or 
php minichan help
```

### Serve
To serve the server simply type or copy the command below in the terminal.
```bash
php minichan serve
```

# Config
The Config class is a configuration class within the Minichan\Config namespace. Its purpose is to define gRPC services and middlewares for a PHP application. This class is designed to be static, providing methods to retrieve arrays of gRPC services and middlewares.

**`Class` Declaration**
```php
    declare(strict_types=1);

    namespace Minichan\Config;

    use Minichan\Middleware\LoggingMiddleware;
    use Minichan\Middleware\ServiceHandler;
    use Minichan\Middleware\TraceMiddleware;
    use Minichan\Services\AuthService;

    /**
     * Configuration class for defining gRPC services and middlewares.
     */
    class Config
    {
        // Class implementation...
    }
```
The **`getServices`** method is responsible for returning an array of gRPC services that should be registered in the application. In the provided code, it returns an array containing the **...\Services\Config\Config::registerServices()** class. This assume that in the **Services\Config\Config** file have a register Service.

```php
    /**
     * Get an array of gRPC services to be registered.
     *
     * @return array
     */
    public static function getServices(): array
    {
        return [
            ...\Services\Config\Config::registerServices(),
        ];
    }
```

The **`getMiddlewares`** method returns an array of middleware instances that should be added to the application. In the provided code, it returns instances of LoggingMiddleware and TraceMiddleware. These classes are assumed to be middleware components that provide logging and tracing functionality, respectively.

```php
    /**
     * Get an array of middlewares to be added.
     *
     * @return array
     */
    public static function getMiddlewares(): array
    {
        return [
            new LoggingMiddleware(),
            new TraceMiddleware(),
        ];
    }
```

# MIDDLEWARE
The **`ServiceHandler`** middleware is designed to handle GRPC service requests. It processes incoming requests, executes the corresponding service method, and generates a response with appropriate context and output. The middleware is responsible for managing errors, logging relevant information, and ensuring a standardized response structure.

## Methods

```php
public function process(\Minichan\Grpc\Request $request, \Minichan\Grpc\RequestHandlerInterface $handler): \Minichan\Grpc\MessageInterface {
    // implementation logic
}
```

**Description:**

Processes the incoming GRPC service request, executes the corresponding service method, and generates a response.

**Parameters:**

- `$request (\Minichan\Grpc\Request):`
  The incoming GRPC request.

- `$handler (\Minichan\Grpc\RequestHandlerInterface):`
  The request handler interface.

**Returns:**

- `\Minichan\Grpc\MessageInterface:`
  The GRPC response message.

## Exception Handling

### `GRPCException`

- Captures `GRPCException` and logs the error details.
- Updates the context with the error status and message.
- Sets the response output to an empty string.

### `Swoole\Exception`

- Captures `\Swoole\Exception` (Swoole-specific exceptions) and logs the error details.
- Updates the context with the Swoole error status and message.
- Sets the response output to an empty string.

### Other Exceptions (`Throwable`)

- Captures general exceptions (implementing `Throwable`) and rethrows as `InvokeException`.
- Logs the error details.
- The `InvokeException` includes the original exception as the previous exception.

## Context Management

- Extracts service, method, and context information from the incoming request.
- Checks if the requested service is available and throws a `NotFoundException` if not.
- Updates the context with a successful status (`Status::OK`) after handling the request.

## Logging

- Utilizes the `Util::log` method for logging errors with appropriate log levels.
- Logs error messages, error codes, and stack traces for better diagnostics.

## Response Generation

- Returns a `Response` object encapsulating the updated context and output.

## The ServiceHandler being Constructed
The **`ServiceHandler`** already have it's instance in the StackHandler in Server, that you will find in the Grpc folder Server.php file.
```php
// Server.php
    /**
     * Server constructor.
     *
     * @param string $host
     * @param int    $port
     * @param int    $mode
     * @param int    $sockType
     */
    public function __construct(string $host, int $port = 0, int $mode = SWOOLE_TCP, int $sockType = SWOOLE_SOCK_TCP)
    {
        $this->host     = $host;
        $this->port     = $port;
        $this->mode     = $mode;
        $this->sockType = $sockType;

        $server = new \Swoole\Http\Server($this->host, $this->port, $this->mode, $this->sockType);
        $server->on('start', function () {
            Util::log(SWOOLE_LOG_INFO, "GRPC Server Started: {$this->host}:{$this->port}");
        });
        $this->server   = $server;
        $this->handler  = (new StackHandler())->add(new ServiceHandler());
    }
```

# Server Class
The Server class is a crucial component of the Minichan application, responsible for handling GRPC server configurations, managing services, and processing incoming requests. It utilizes the Swoole extension to create an HTTP server capable of handling GRPC requests.

## Class Structure

### Dependencies

- `Closure`: A PHP built-in class representing anonymous functions.
- `Minichan\Config\Constant`: Constants related to Minichan configuration.
- `Minichan\Config\Status`: Constants related to status codes.
- `Minichan\Exception\GRPCException`: Exception class for GRPC-related errors.
- `Minichan\Exception\InvokeException`: Exception class for invocation errors.
- `Minichan\Middleware\MiddlewareInterface`: Interface for middleware implementations.
- `Minichan\Middleware\ServiceHandler`: Middleware for handling GRPC service requests.
- `Minichan\Middleware\StackHandler`: Middleware stack handler.
- `Minichan\Grpc\Context`: Context for managing values associated with a specific request.
- `Minichan\Grpc\Util`: Utility class for GRPC-related operations.
- `Minichan\Grpc\Request`: Class representing a GRPC request.
- `Minichan\Grpc\Response`: Class representing a GRPC response.
- `Minichan\Grpc\ServiceContainer`: Container for managing GRPC services.

### Properties

- `$host`, `$port`, `$mode`, `$sockType`: Server configuration parameters.
- `$settings`: Additional server settings.
- `$services`: Container for registered GRPC services.
- `$workerContexts`, `$workerContext`: Worker-specific contexts for executing closures.
- `$server`, `$handler`: Swoole server instance and middleware handler.

### Constructor

#### `__construct(string $host, int $port = 0, int $mode = SWOOLE_TCP, int $sockType = SWOOLE_SOCK_TCP)`

**Description:**
Initializes the server with specified host, port, mode, and socket type.
Creates a Swoole HTTP server and sets up event handlers.

### Public Methods

#### `withWorkerContext(string $context, Closure $callback): self`

**Description:**
Registers a closure to be executed with worker context.

#### `addMiddleware(MiddlewareInterface $middleware): self`

**Description:**
Adds a middleware to the server's middleware stack.

#### `addMiddlewares(array $middlewares): self`

**Description:**
Adds an array of middlewares to the server's middleware stack.

#### `set(array $settings): self`

**Description:**
Sets server settings.

#### `start(): void`

**Description:**
Starts the Swoole server and initializes event handlers.

#### `on(string $event, Closure $callback): self`

**Description:**
Registers a callback for a specific Swoole server event.

#### `register(string $class): self`

**Description:**
Registers a GRPC service based on the provided class.

#### `registerServices(array $serviceClasses): self`

**Description:**
Registers multiple GRPC services based on an array of class names.

### Private Methods

#### `process(\Swoole\Http\Request $rawRequest, \Swoole\Http\Response $rawResponse): void`

**Description:**
Handles an incoming GRPC request.

#### `initWorkerContext(): void`

**Description:**
Initializes the worker context with values and closures.

#### `send(Response $response): void`

**Description:**
Sends a GRPC response.

#### `validateRequest(\Swoole\Http\Request $request): void`

**Description:**
Validates a GRPC request for essential headers.

#### `validateServiceClass(string $class): void`

**Description:**
Validates if the provided class is a valid GRPC service class.

#### `handleGRPCException(GRPCException $e, Context $context): void`

**Description:**
Handles a GRPC exception by logging and updating the context.


## Troubleshooting
If you encounter issues while using , consider the following steps:

1. Check the Command Syntax: Ensure that you are using the correct syntax for the command.

2. Review Error Messages: Examine any error messages displayed in the console for clues about the issue.