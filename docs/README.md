# minichan-v1

<img src="/assets/logo.svg" alt="Project Logo" width="200" height="200">

A PHP gRPC library utilizing Swoole coroutines, encompassing a protoc code generator, server, and client components.

# OVERVIEW
**minichan-v1** is a PHP gRPC library that leverages Swoole coroutines. It encompasses a protoc code generator, server, and client components to facilitate the development of gRPC-based applications. 
# Table of Contents
- [Installation](#installation)
- [Protoc Code Generation](#generate-php-code-via-proto)
- [Service](#service)
- [Basic gRPC Server](#basic-grpc-server)
- [Basic gRPC Client](#basic-grpc-client)
- [CLI minichan](#cli-minichan)
- [SessionHandler](#session)
- [Unidirectional](#unidirectional)
- [Stream](#stream)
- [Different Language Client](#different-language-client)
- [Main Class Components](#main-class-components)
- [Troubleshooting](#troubleshooting)

# INSTALLATION
Swoole >= 5.1.0: https://wiki.swoole.com/#/environment
It needs to be turned on --enable-http2

[Protoc](https://github.com/protocolbuffers/protobuf) is a code generator for protobuf data structures, tasked with converting .proto files into language-specific classes and structs for program implementation.

[minichan](https://github.com/Leincentes/minichan-grpc/releases) is a protoc plug-in created by the grpc library, designed to generate server and client code for services.

# GENERATE PHP CODE via .proto
First create a folder proto and copy this code to the proto file.

```proto

syntax = "proto3";

package PHP.SampleApp;

service SampleAppService {
    rpc SampleMethod (stream Request) returns (stream Response);
}

message Request {
    string sample_request = 1;
}

message Response{
    string sample_response = 1;
}

```

Then use protoc to generate the code:
```bash
protoc --php_out=. --grpc_out=. --plugin=protoc-gen-grpc=lib/Bin/minichan-grpc proto/sample.proto
```
When the command is executed, the following files will be generated in the current directory:
```bash
|--GPBMetadata/Proto
| `--Sample.php
|--PHP/SampleApp
| `--Request.php
| `--Response.php
| `--SampleAppServiceClient.php
| `--SampleAppServiceInterface.php
``--sample.proto

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
Creation of sample service class for the server side. 
```php

declare(strict_types=1);

namespace Services;

use PHP\SampleApp\Response;
use PHP\SampleApp\SampleAppServiceInterface;

class SampleService implements SampleAppServiceInterface {
    private Response $response;

    public function __construct() {
        $this->response = new Response();
    }
    public function SampleMethod(\Minichan\Grpc\ContextInterface $ctx, \PHP\SampleApp\Request $request): Response {

        $this->response->setSampleResponse('Hi from server ' . $request->getSampleRequest());

        return $this->response;
    }
}

```

## Add the Service in the Config
In the folder Services you'll find Cofig folder where Config.php reside. This is where you can register the Service you created.
```php

declare(strict_types=1);

namespace Services\Config;

use Services\SampleService;

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
            SampleService::class,
        ];
    }
}

```

# Basic gRPC Server
All you needed to do is to execute the file, it will init all the neccessary for the server.
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

# Basic gRPC Client
A basic example how the client will be handled.
```php
// client.php
declare(strict_types=1);

use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use PHP\SampleApp\Request;
use Swoole\Coroutine as Co;

require __DIR__ . '/vendor/autoload.php';

Co::create(function () {
    $conn = (new Client(Constant::SERVER_HOST, Constant::SERVER_PORT, Constant::GRPC_CALL))
        ->set([
            'open_http2_protocol' => 1,
        ])
        ->connect();

    $sampleService = new \PHP\SampleApp\SampleAppServiceClient($conn);

    $req = new Request();
    $req->setSampleRequest('This is a sample request.');
    
    $data = $sampleService->SampleMethod($req)->getSampleResponse();

    print_r($data); // Hi from server This is a sample request

    $conn->close();
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
# Session
Transparently starting and stopping sessions, with session IDs stored in cookies or query strings, utilizing either native or custom session ID generation, and featuring automatic session data persistence.

## Usage
To use the `SessionHandler` class, you can instantiate it with the necessary dependencies and then invoke it as a callable. Here's an example of how to use it:

Wrap your request handling middleware into the session handler:
```php
require 'vendor/autoload.php';

use Minichan\Grpc\SessionHandler;

$server = new \Swoole\Http\Server('127.0.0.1', 8080);
$server->set([
    // set your own settings
]);
$server->on('request', new SessionHandler(function ($request, $response) {
    $_SESSION['data'] ??= rand();
    $response->end($_SESSION['data']);
}));

$server->start();
```
# Unidirectional
Unidirectional streaming, also known as server streaming or client streaming, is a communication pattern where one party continuously sends data to the other party. 
## Usage
Generation of protofile
```proto
syntax = "proto3";

package generated.PHP.Hello;

service Greeter {
  rpc SayHello (HelloRequest) returns (HelloReply);
}

message HelloRequest {
  string name = 1;
}

message HelloReply {
  string message = 1;
}

```
### Hello Service
A sample hello service class.
```php
declare(strict_types=1);

namespace Services;
use Generated\PHP\Hello\GreeterInterface;
use Generated\PHP\Hello\HelloReply;

class HelloService implements GreeterInterface {
    private HelloReply $res;
    public function __construct() {
        $this->res = new HelloReply();
    }
    public function SayHello(\Minichan\Grpc\ContextInterface $ctx, \Generated\PHP\Hello\HelloRequest $request): HelloReply {
        return $this->res->setMessage('Hi from server. My name is ' . $request->getName());
    }
}
```
### Hello Client
A sample client for the service class hello.
```php
declare(strict_types=1);

use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use Swoole\Coroutine as Co;

require __DIR__ . '/vendor/autoload.php';

Co::create(function () {
    $conn = (new Client(Constant::SERVER_HOST, Constant::SERVER_PORT, Constant::GRPC_CALL))
        ->set([
            'open_http2_protocol' => 1,
        ])
        ->connect();

        $helloService = new \Helloworld\GreeterClient($conn);

        $req = new \Helloworld\HelloRequest();
        $req->setName('Jake');

        $data = $helloService->SayHello($req)->getMessage();

        print_r($data); // Hi from server. My name is Jake.

        $conn->close();
});
```

# Streams
Streams are crucial for real-time data transmission, facilitating continuous communication between endpoints without the overhead of establishing new connections for each exchange. 
## Usage
Generation of protofile
```proto
// stream.proto
syntax = "proto3";

package PHP.StreamApp;

service StreamingService {
    rpc StreamMessages (stream Message) returns (stream Message);
}

message Message {
    string text = 1;
}
```
### Stream Service
A sample of stream service class.
```php
declare(strict_types=1);

namespace Services;

use PHP\StreamApp\Message;
use PHP\StreamApp\StreamingServiceInterface;

class StreamingService implements StreamingServiceInterface {
    private Message $message;
    public function __construct() {
        $this->message = new Message();
    }
    public function StreamMessages(\Minichan\Grpc\ContextInterface $ctx, Message $request): Message {    
        return $this->message->setText("Hello from server this is stream " . (string)rand(). "\n");
    }
}

```

### Stream Client
Utilizing the coroutine of swoole for more optimize bidirectional streaming.
```php
declare(strict_types=1);

use Minichan\Config\Constant;
use Minichan\Grpc\Client;
use PHP\StreamApp\Message;
use Swoole\Coroutine as Co;

require __DIR__ . '/vendor/autoload.php';

// Coroutine to handle bidirectional streaming
Co::create(function () {
    $conn = (new Client(Constant::SERVER_HOST, Constant::SERVER_PORT, Constant::GRPC_CALL))
        ->set([
            'open_http2_protocol' => 1,
        ])
        ->connect();

    $streamService = new \PHP\StreamApp\StreamingServiceClient($conn);

    // Infinite loop to continuously read and send messages
    while (true) {
        // Receive message from gRPC server
        $message = $streamService->StreamMessages(new Message())->getText();

        print_r($message); // Hello from server this is stream 982283289

        // Sleep for a short interval to prevent CPU usage spike
        Co::sleep(1); 
    }
});
?>

```

# Different Language Client
The server can accomodate different languages of client or server service for request. It enhances the overall accessibility and usability of the server's services across diverse client environments.

## Python 
A sample as a client. Assuming you already generated the needed stub.

```python

import grpc
import stream_pb2 # The generated Stub
import stream_pb2_grpc # The generated Stub
import time

def generate_messages():
    while True:
        try:
            with grpc.insecure_channel('localhost:9502') as channel:
                stub = stream_pb2_grpc.StreamingServiceStub(channel)
                
                messages = [
                    stream_pb2.Message(text="Hello"),
                    stream_pb2.Message(text="World")
                ]
                
                responses = stub.StreamMessages(iter(messages))
                
                for response in responses:
                    print(response.text)
        except grpc.RpcError as e:
            print("Error:", e)
            
        time.sleep(1)  

if __name__ == '__main__':
    generate_messages() # Hello from server this is stream 214338839

```

## Javascript
A sample as a client. Assuming you already generated the needed stub.

```javascript

const grpc = require('@grpc/grpc-js');
const { StreamingServiceClient } = require('./stream_grpc_pb'); // the generated stub
const { Message } = require('./stream_pb'); // the generated stub

const client = new StreamingServiceClient('localhost:9502', grpc.credentials.createInsecure());
setInterval(() => {

const call = client.streamMessages();

call.on('data', function(response) {
  console.log($response.getText());  // Hello from server this is stream 214338839
});

call.end();

}, 1000)

```

## Go
A sample as a client. Assuming you already generated the needed stub.
```go
package main

import (
	"context"
	"log"
	"os"
	"time"

	pb "github.com/grpc/grpc/hello" // the generated stub

	"google.golang.org/grpc"
)

const (
	address     = "localhost:9502"
	defaultName = "Jake"
)

func main() {
	conn, err := grpc.Dial(address, grpc.WithInsecure())
	if err != nil {
		log.Fatalf("Did not connect: %v", err)
	}
	defer conn.Close()

	c := pb.NewGreeterClient(conn)

	name := defaultName
	if len(os.Args) > 1 {
		name = os.Args[1]
	}

	ctx, cancel := context.WithTimeout(context.Background(), time.Second)
	defer cancel()

	r, err := c.SayHello(ctx, &pb.HelloRequest{Name: name})
	if err != nil {
		log.Fatalf("Could not greet: %v", err)
	}
	log.Printf(r.Message) // Hi from server. My name is Jake
}

```

# Main Class Components

## Server Class
The `Server` class is a crucial component of the Minichan application, responsible for handling GRPC server configurations, managing services, and processing incoming requests. It utilizes the Swoole extension to create an HTTP server capable of handling GRPC requests.

### Class Structure

#### Dependencies

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

#### Properties

- `$host`, `$port`, `$mode`, `$sockType`: Server configuration parameters.
- `$settings`: Additional server settings.
- `$services`: Container for registered GRPC services.
- `$workerContexts`, `$workerContext`: Worker-specific contexts for executing closures.
- `$server`, `$handler`: Swoole server instance and middleware handler.

#### Constructor

##### `__construct(string $host, int $port = 0, int $mode = SWOOLE_TCP, int $sockType = SWOOLE_SOCK_TCP)`

**Description:**
Initializes the server with specified host, port, mode, and socket type.
Creates a Swoole HTTP server and sets up event handlers.

#### Public Methods

##### `withWorkerContext(string $context, Closure $callback): self`

**Description:**
Registers a closure to be executed with worker context.

##### `addMiddleware(MiddlewareInterface $middleware): self`

**Description:**
Adds a middleware to the server's middleware stack.

##### `addMiddlewares(array $middlewares): self`

**Description:**
Adds an array of middlewares to the server's middleware stack.

##### `set(array $settings): self`

**Description:**
Sets server settings.

##### `start(): void`

**Description:**
Starts the Swoole server and initializes event handlers.

##### `on(string $event, Closure $callback): self`

**Description:**
Registers a callback for a specific Swoole server event.

##### `register(string $class): self`

**Description:**
Registers a GRPC service based on the provided class.

##### `registerServices(array $serviceClasses): self`

**Description:**
Registers multiple GRPC services based on an array of class names.

### Private Methods

##### `process(\Swoole\Http\Request $rawRequest, \Swoole\Http\Response $rawResponse): void`

**Description:**
Handles an incoming GRPC request.

##### `initWorkerContext(): void`

**Description:**
Initializes the worker context with values and closures.

##### `send(Response $response): void`

**Description:**
Sends a GRPC response.

##### `validateRequest(\Swoole\Http\Request $request): void`

**Description:**
Validates a GRPC request for essential headers.

##### `validateServiceClass(string $class): void`

**Description:**
Validates if the provided class is a valid GRPC service class.

##### `handleGRPCException(GRPCException $e, Context $context): void`

**Description:**
Handles a GRPC exception by logging and updating the context.

## Config Class
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

## Middleware Class
The **`ServiceHandler`** middleware is designed to handle GRPC service requests. It processes incoming requests, executes the corresponding service method, and generates a response with appropriate context and output. The middleware is responsible for managing errors, logging relevant information, and ensuring a standardized response structure.

### Methods

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

### Exception Handling

#### `GRPCException`

- Captures `GRPCException` and logs the error details.
- Updates the context with the error status and message.
- Sets the response output to an empty string.

#### `Swoole\Exception`

- Captures `\Swoole\Exception` (Swoole-specific exceptions) and logs the error details.
- Updates the context with the Swoole error status and message.
- Sets the response output to an empty string.

#### Other Exceptions (`Throwable`)

- Captures general exceptions (implementing `Throwable`) and rethrows as `InvokeException`.
- Logs the error details.
- The `InvokeException` includes the original exception as the previous exception.

### Context Management

- Extracts service, method, and context information from the incoming request.
- Checks if the requested service is available and throws a `NotFoundException` if not.
- Updates the context with a successful status (`Status::OK`) after handling the request.

### Logging

- Utilizes the `Util::log` method for logging errors with appropriate log levels.
- Logs error messages, error codes, and stack traces for better diagnostics.

### Response Generation

- Returns a `Response` object encapsulating the updated context and output.

### The ServiceHandler being Constructed
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

## Client Class
The `Client` class in the minichan-v1 library provides functionality for creating gRPC client connections and sending/receiving messages to/from a remote endpoint.

### Constructor Parameters

- `$host`: The hostname or IP address of the remote endpoint.
- `$port`: The port number of the remote endpoint.
- `$mode`: The mode of gRPC communication. It can be either `Constant::GRPC_CALL` for unary calls or `Constant::GRPC_CLIENT_STREAM` for client-side streaming.

### Methods

- `set(array $settings): self`: Set additional settings for the client.
- `connect(): self`: Establish a connection to the remote endpoint.
- `stats(): array`: Get the statistics of the client.
- `close()`: Close the connection to the remote endpoint.
- `send($method, $message, $type = 'proto', $user_agent = 'minichan/v1')`: Send a message to the remote endpoint.
- `recv($streamId, $timeout = -1)`: Receive data from the server.
- `push($streamId, $message, $type = 'proto', $end = false)`: Push a message to the remote endpoint (used in client-side streaming mode).

### Usage
```php

use Minichan\Grpc\Client;

// Instantiate the Client
$client = new Client($host, $port, $mode);

// Set additional settings if needed
$client->set(['timeout' => 5000]);

// Connect to the remote endpoint
$client->connect();

// Send a message to the server
$streamId = $client->send($method, $message, $type);

// Receive a response from the server
$response = $client->recv($streamId);

// Close the connection
$client->close();

```

## Context Class

The `Context` class in the minichan-v1 library provides a context container for storing and retrieving key-value pairs.

### Constructor

- `Context(array $values)`: Constructs a new context with the specified initial values.

### Methods

- `withValue(string $key, $value): ContextInterface`: Creates a new context with the specified key and value.
- `getValue(string $key, $default = null)`: Retrieves the value associated with the specified key from the context.
- `getValues(): array`: Retrieves all values stored in the context.
- `count(): int`: Retrieves the number of values in the context.

## Message Class

The `Message` class in the minichan-v1 library represents a message with an associated context.

### Constructor

- `Message(Context $context, $message)`: Constructs a new message with the specified context and message content.

### Methods

- `getMessage()`: Retrieves the actual message content.
- `getContext()`: Retrieves the context associated with the message.

## Request Class

The `Request` class in the minichan-v1 library represents a gRPC request with an associated context, service, method, and payload.

### Constructor

- `Request(Context $context, string $service, string $method, string $payload)`: Constructs a new request with the specified context, service name, method name, and payload.

### Methods

- `getService()`: Retrieves the gRPC service name.
- `getMethod()`: Retrieves the gRPC method name.
- `getPayload()`: Retrieves the payload of the request.
- `getContext()`: Retrieves the context associated with the request.
- `withContext(Context $context)`: Sets a new context for the request.
- `getMessage()`: Retrieves the actual message content.

## Response Class

The `Response` class in the minichan-v1 library represents a gRPC response with an associated context and payload.

### Constructor

- `Response(Context $context, string $payload)`: Constructs a new response with the specified context and payload.

### Methods

- `getPayload()`: Retrieves the payload of the response.
- `getContext()`: Retrieves the context associated with the response.
- `getMessage()`: Retrieves the actual message content.

## ServiceContainer Class

The `ServiceContainer` class in the minichan-v1 library represents a container for managing gRPC services, including service discovery and method invocation.

### Constructor

- `ServiceContainer(string $interface, ServiceInterface $service)`: Constructs a new service container with the specified service interface and instance.

### Methods

- `getName()`: Retrieves the name of the service.
- `getService()`: Retrieves the service instance.
- `getMethods()`: Retrieves an array of discovered methods for the service.
- `handle(Request $request)`: Handles a gRPC request for a specific method, executing the method and returning the serialized output.

# Troubleshooting
If you encounter issues while using , consider the following steps:

1. Check the Command Syntax: Ensure that you are using the correct syntax for the command.

2. Review Error Messages: Examine any error messages displayed in the console for clues about the issue.