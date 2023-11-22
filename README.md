## grpc-proto-v1

A PHP gRPC library utilizing Swoole coroutines, encompassing a protoc code generator, server, and client components.

## OVERVIEW











## INSTALLATION
Swoole >= 5.1.0: https://wiki.swoole.com/#/environment
It needs to be turned on --enable-http2

[Protoc](https://github.com/protocolbuffers/protobuf) is a code generator for protobuf data structures, tasked with converting .proto files into language-specific classes and structs for program implementation.

[grpc]() is a protoc plug-in created by the grpc library, designed to generate server and client code for services.

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


## CLI
Introduction <a name="introduction"></a>
A command-line interface for generating various components in your project. This tool aims to streamline the process of creating classes, interfaces, and other components in your project.

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

## Troubleshooting
If you encounter issues while using , consider the following steps:

1. Check the Command Syntax: Ensure that you are using the correct syntax for the command.

2. Review Error Messages: Examine any error messages displayed in the console for clues about the issue.