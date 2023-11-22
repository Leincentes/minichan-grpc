# minichan-v1

A PHP gRPC library utilizing Swoole coroutines, encompassing a protoc code generator, server, and client components.

## OVERVIEW











## INSTALLATION
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
protoc --php_out=. --grpc_out=. --plugin=protoc-gen-grpc=./Bin/minichan proto/userauth.proto
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
        "PHP\\UserService\\": "PHP/UserService/"
    }
}

```
Modify and execute to make it effective, 
```bash 
composer dump-autoload
```

## Basic Serve of gRPC Server
Under the lib folder you will find the run.php
All you needed to do is to execute the file.
```php
// run.php

declare(strict_types= 1);

use Minichan\Config\Config;
use Minichan\Config\Constant;

require_once './vendor/autoload.php';

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
        'worker_num' => 4,                  // Number of worker processes
        'open_http2_protocol' => 1,         // Enable HTTP2 protocol
        'enable_coroutine' => true,         // Enable coroutine support
    ]);

// Start the gRPC server
$server->start();

```

## Basic Client
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


# CLI
A command-line interface for generating various classes and interfaces. This tool aims to streamline the process of creating classes and interfaces.

## Usage
General Usage <a name="general-usage"></a>
```bash
php lib/cli.php [command] [arguments]
```

Available Commands <a name="available-commands"></a>
Use the following command to display a list of available commands:
```bash
php lib/cli.php help
```

Command Reference <a name="command-reference"></a>

generate:cli <a name="generate-cli"></a>
Generate a new CLI class or interface.
```bash
php lib/cli.php generate:cli [CliName] [Interface|Class]
```

generate:config <a name="generate-config"></a>
Generate a new configuration class or interface.
```bash
php lib/cli.php generate:config [ConfigName] [Interface|Class]
```

generate:database <a name="generate-database"></a>
Generate a new database-related class or interface.
```bash
php lib/cli.php generate:database [DatabaseName] [Interface|Class]
```

generate:exception <a name="generate-exception"></a>
Generate a new exception class or interface.
```bash
php lib/cli.php generate:exception [ExceptionName] [Interface|Class]
```

generate:grpc <a name="generate-grpc"></a>
Generate a new grpc-related class or interface.
```bash
php lib/cli.php generate:grpc [GrpcName] [Interface|Class]
```

generate:middleware <a name="generate-middleware"></a>
Generate a new middleware class or interface.
```bash
php lib/cli.php generate:middleware [MiddlewareName] [Interface|Class]
```

generate:services <a name="generate-services"></a>
Generate a new services-related class or interface.
```bash
php lib/cli.php generate:services [ServicesName] [Interface|Class]
```

# Config
The Config class is a configuration class within the Minichan\Config namespace. Its purpose is to define gRPC services and middlewares for a PHP application. This class is designed to be static, providing methods to retrieve arrays of gRPC services and middlewares.

**`Class` declaration**
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
**`getServices` Method**
```php
    /**
     * Get an array of gRPC services to be registered.
     *
     * @return array
     */
    public static function getServices(): array
    {
        return [
            AuthService::class,
        ];
    }
```
The **`getServices`** method is responsible for returning an array of gRPC services that should be registered in the application. In the provided code, it returns an array containing the 'AuthService' class. It is assumed that the 'AuthService' class is a gRPC service that needs to be registered.

**`getMiddlewares` Method**
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
The **`getMiddlewares`** method returns an array of middleware instances that should be added to the application. In the provided code, it returns instances of LoggingMiddleware and TraceMiddleware. These classes are assumed to be middleware components that provide logging and tracing functionality, respectively.

# DATABASE
A minimal PHP database helper for ease development.
## Method Description:

The **`select`** method is designed to execute a SELECT query on a specified table, offering flexibility through various parameter combinations.

## Parameters:

- **$table (string):**
  The name of the table to query.

- **$columns (array):**
  An array of columns to be selected.

- **$where (array):**
  An associative array representing the WHERE conditions for the query.

- **$callback (callable|null):**
  A callback function that can be applied to each row of the result set (optional).

## Return Value:

- If no callback is provided:
  - The method returns an array containing the selected rows.

- If a callback is provided:
  - The method returns null.

## Usage:

```php
// without callback
$result = select("your_table", ["column1", "column2"], ["column3" => "value"]);

// with callback
select("your_table", ["column1", "column2"], ["column3" => "value"], function ($row) {
    // custom processing for each row
});

```

## Method Description:

The **`insert`** method facilitates the insertion of one or more records into a specified table.

## Parameters:

- **$table (string):**
  The name of the table to insert records into.

- **$values (array):**
  An associative array representing the values to be inserted, where keys are column names.

- **$primaryKey (string|null):**
  The primary key column name (optional).

## Return Value:

- The PDOStatement object on success.

- Null on failure.

## Usage Example:

```php
// insert 
$inserted = insert("your_table", ["column1" => "value1", "column2" => "value2"]);

// with specified primary key
$inserted = insert("your_table", ["column1" => "value1", "column2" => "value2"], "id");

```

## Method Description:

The **`update`** method is employed to modify records in a specified table based on the provided conditions.

## Parameters:

- **$table (string):**
  The name of the table to update records in.

- **$values (array):**
  An associative array representing the new values to be set.

- **$where (array):**
  An associative array representing the WHERE conditions for the update.

## Return Value:

- The PDOStatement object on success.

- Null on failure.

## Usage Example:

```php
// update 
$updated = update("your_table", ["column1" => "new_value"], ["column2" => "value2"]);

// with specified condition
$updated = update("your_table", ["column1" => "new_value"], ["column2" => "value2", "column3" => "value3"]);

```

## Method Description:

The **`delete`** method is utilized to remove records from a specified table based on the provided conditions.

## Parameters:

- **$table (string):**
  The name of the table to delete records from.

- **$where (array):**
  An associative array representing the WHERE conditions for the delete.

## Return Value:

- The PDOStatement object on success.

- Null on failure.

## Usage Example:

```php
// delete 
$deleted = delete("your_table", ["column1" => "value1"]);

// with specified condition
$deleted = delete("your_table", ["column2" => "value2", "column3" => "value3"]);

```

## Method Description:

The **`get`** method is designed to execute a SELECT query and retrieve a single result, which can be either a single row or a single column value.

## Parameters:

- **$table (string):**
  The name of the table to query.

- **$columns (array|string):**
  An array or string representing the columns to be selected.

- **$where (array):**
  An associative array representing the WHERE conditions for the query.

## Return Value:

- The result of the SELECT query, which can be a single row or a single column value.

## Usage Example:

```php
// retrieving a single row
$resultRow = get("your_table", ["column1", "column2"], ["column3" => "value"]);

// retrieving a single column value
$singleValue = get("your_table", "column1", ["column2" => "value2"]);

```

## Method Description:

The **`has`** method is employed to determine if records exist in a table based on the provided conditions.

## Parameters:

- **$table (string):**
  The name of the table to query.

- **$where (array):**
  An associative array representing the WHERE conditions for the query.

## Return Value:

- `true` if records exist based on the conditions.

- `false` otherwise.

## Usage Example:

```php
// checking if records exist
$hasRecords = has("your_table", ["column1" => "value1"]);

// checking with multiple conditions
$hasRecords = has("your_table", ["column2" => "value2", "column3" => "value3"]);

```

## Method Description:

The **`rand`** method executes a SELECT query and retrieves a random result, which can be a random value from a specific column.

## Parameters:

- **$table (string):**
  The name of the table to query.

- **$column (array|string):**
  An array or string representing the column to be selected for the random result.

- **$where (array):**
  An associative array representing the WHERE conditions for the query.

## Return Value:

- The random result of the SELECT query, which can be a single value.

## Usage Example:

```php
// retrieving a random value from a specific column
$randomValue = rand("your_table", "column1");

// with specified conditions
$randomValue = rand("your_table", "column2", ["column3" => "value3"]);

```

## Method Description:

The **`count`** method is used to determine the number of records in a table based on the provided conditions.

## Parameters:

- **$table (string):**
  The name of the table to query.

- **$where (array):**
  An associative array representing the WHERE conditions for the query.

## Return Value:

- The number of records that match the conditions.

## Usage Example:

```php
// counting records
$recordCount = count("your_table");

// with specified conditions
$filteredCount = count("your_table", ["column1" => "value1", "column2" => "value2"]);

```

## Method Descriptions:

### max Method

The **`max`** method calculates the maximum value for a specified column in the table based on the provided conditions.

### min Method

The **`min`** method calculates the minimum value for a specified column in the table based on the provided conditions.

### avg Method

The **`avg`** method calculates the average value for a specified column in the table based on the provided conditions.

### sum Method

The **`sum`** method calculates the sum of values for a specified column in the table based on the provided conditions.

## Parameters:

- **$table (string):**
  The name of the table to query.

- **$column (string):**
  The name of the column for which the aggregate value is calculated.

- **$where (array):**
  An associative array representing the WHERE conditions for the query.

## Return Value:

- The calculated aggregate value for the specified column (as a string).

## Usage Examples:

```php
// using max
$maxValue = max("your_table", "column1");

// using min
$minValue = min("your_table", "column2");

// using avg
$averageValue = avg("your_table", "column3", ["column4" => "value"]);

// using sum
$totalSum = sum("your_table", "column5", ["column6" => "value"]);

```

# MIDDLEWARE
The **`ServiceHandler`** middleware is designed to handle GRPC service requests. It processes incoming requests, executes the corresponding service method, and generates a response with appropriate context and output. The middleware is responsible for managing errors, logging relevant information, and ensuring a standardized response structure.

## Methods

### `process(\Minichan\Grpc\Request $request, \Minichan\Grpc\RequestHandlerInterface $handler): \Minichan\Grpc\MessageInterface`

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
The **`ServiceHandler`** is already will have it's instance in the StackHandler in Server that you will find in the Grpc folder Server.php file.
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



## Troubleshooting
If you encounter issues while using , consider the following steps:

1. Check the Command Syntax: Ensure that you are using the correct syntax for the command.

2. Review Error Messages: Examine any error messages displayed in the console for clues about the issue.