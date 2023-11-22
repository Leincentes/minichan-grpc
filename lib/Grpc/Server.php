<?php

declare(strict_types=1);
namespace Minichan\Grpc;

use Closure;
use Minichan\Config\Constant;
use Minichan\Config\Status;
use Minichan\Exception\GRPCException;
use Minichan\Exception\InvokeException;
use Minichan\Middleware\MiddlewareInterface;
use Minichan\Middleware\ServiceHandler;
use Minichan\Middleware\StackHandler;
use TypeError;

final class Server
{
    // Server Configuration
    private string $host;
    private int $port;
    private int $mode;
    private int $sockType;
    private array $settings = [];

    // Service Container
    private array $services = [];

    // Worker Contexts
    private array $workerContexts = [];
    private $workerContext;

    // Swoole Server and Handler
    private $server;
    private $handler;

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

    /**
     * Register a closure to be executed with worker context.
     *
     * @param string  $context
     * @param Closure $callback
     *
     * @return $this
     */
    public function withWorkerContext(string $context, Closure $callback): self
    {
        $this->workerContexts[$context] = $callback;
        return $this;
    }

    /**
     * Add middleware to the server.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return $this
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->handler = $this->handler->add($middleware);
        return $this;
    }


    /**
     * Add an array of middleware to the server.
     *
     * @param array $middlewares
     *
     * @return $this
     */
    public function addMiddlewares(array $middlewares): self
    {
        foreach ($middlewares as $middleware) {
            if (!$middleware instanceof MiddlewareInterface) {
                throw new \InvalidArgumentException('Middleware must implement MiddlewareInterface.');
            }

            $this->handler = $this->handler->add($middleware);
        }

        return $this;
    }

    /**
     * Set server settings.
     *
     * @param array $settings
     *
     * @return $this
     */
    public function set(array $settings): self
    {
        $this->settings = array_merge($this->settings, $settings ?? []);
        return $this;
    }

    /**
     * Start the server.
     *
     * @return void
     */
    public function start(): void
    {
        $this->server->set($this->settings);

        $this->server->on('workerStart', function (\Swoole\Server $server, int $workerId) {
            $this->initWorkerContext();
        });

        $this->server->on('request', function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) {
            $this->process($request, $response);
        });

        $this->server->start();
    }

    /**
     * Handle an event.
     *
     * @param string  $event
     * @param Closure $callback
     *
     * @return $this
     */
    public function on(string $event, Closure $callback): self
    {
        $this->server->on($event, function () use ($callback) {
            $callback->call($this);
        });

        return $this;
    }

    /**
     * Register a gRPC service.
     *
     * @param string $class
     *
     * @return $this
     * @throws TypeError
     */
    public function register(string $class): self
    {
        $this->validateServiceClass($class);

        $instance                             = new $class();
        $service                              = new ServiceContainer($class, $instance);
        $this->services[$service->getName()] = $service;

        return $this;
    }

    /**
     * Register gRPC services based on an array of service class names.
     *
     * @param array $serviceClasses
     *
     * @return $this
     * @throws TypeError
     */
    public function registerServices(array $serviceClasses): self
    {
        foreach ($serviceClasses as $class) {
            $this->validateServiceClass($class);

            $instance                             = new $class();
            $service                              = new ServiceContainer($class, $instance);
            $this->services[$service->getName()] = $service;
        }

        return $this;
    }

    /**
     * Process a gRPC request.
     *
     * @param \Swoole\Http\Request  $rawRequest
     * @param \Swoole\Http\Response $rawResponse
     *
     * @return void
     */
    private function process(\Swoole\Http\Request $rawRequest, \Swoole\Http\Response $rawResponse): void
    {
        $context = new Context([
            'WORKER_CONTEXT'   => $this->workerContext,
            'SERVICES'         => $this->services,
            \Swoole\Http\Request::class  => $rawRequest,
            \Swoole\Http\Response::class => $rawResponse,
            Constant::CONTENT_TYPE       => $rawRequest->header[Constant::CONTENT_TYPE] ?? '',
            Constant::GRPC_STATUS        => Status::UNKNOWN,
            Constant::GRPC_MESSAGE       => '',
        ]);

        try {
            $this->validateRequest($rawRequest);

            [, $service, $method] = explode('/', $rawRequest->server['request_uri'] ?? '');
            $service              = '/' . $service;
            $message              = $rawRequest->getContent() ? substr($rawRequest->getContent(), 5) : '';
            $request              = new Request($context, $service, $method, $message);

            $response = $this->handler->handle($request);
        } catch (GRPCException $e) {
            $this->handleGRPCException($e, $context);
            $response = new Response($context, '');
        }

        $this->send($response);
    }

    /**
     * Initialize worker context.
     *
     * @return void
     */
    private function initWorkerContext(): void
    {
        $this->workerContext = new Context([
            \Minichan\Grpc\Server::class => $this,
            \Swoole\Http\Server::class => $this->server,
        ]);

        foreach ($this->workerContexts as $context => $callback) {
            $this->workerContext = $this->workerContext->withValue($context, $callback->call($this));
        }
    }

    /**
     * Send a gRPC response.
     *
     * @param Response $response
     *
     * @return void
     */
    private function send(Response $response): void
    {
        $context     = $response->getContext();
        $rawResponse = $context->getValue(\Swoole\Http\Response::class);
        $headers     = [
            'content-type' => $context->getValue('content-type'),
            'trailer'      => 'grpc-status, grpc-message',
        ];

        $trailers = [
            Constant::GRPC_STATUS  => $context->getValue(Constant::GRPC_STATUS),
            Constant::GRPC_MESSAGE => $context->getValue(Constant::GRPC_STATUS) == 0 ? 'Success' : $context->getValue(Constant::GRPC_MESSAGE),
        ];

        $payload = pack('CN', 0, strlen($response->getPayload())) . $response->getPayload();

        try {
            foreach ($headers as $name => $value) {
                $rawResponse->header($name, $value);
            }

            foreach ($trailers as $name => $value) {
                $rawResponse->trailer($name, (string) $value);
            }

            $rawResponse->end($payload);
        } catch (\Swoole\Exception $e) {
            Util::log(SWOOLE_LOG_WARNING, $e->getMessage() . ', error code: ' . $e->getCode() . "\n" . $e->getTraceAsString());
        }
    }

    /**
     * Validate a gRPC request.
     *
     * @param \Swoole\Http\Request $request
     *
     * @return void
     * @throws InvokeException
     */
    private function validateRequest(\Swoole\Http\Request $request): void
    {
        if (!isset($request->header['content-type']) || !isset($request->header['te'])) {
            throw InvokeException::create('illegal GRPC request, missing content-type or te header');
        }

        if ($request->header['content-type'] !== 'application/grpc'
            && $request->header['content-type'] !== 'application/grpc+proto'
            && $request->header['content-type'] !== 'application/grpc+json'
        ) {
            throw InvokeException::create("Content-type not supported: {$request->header['content-type']}", Status::INTERNAL);
        }
    }

    /**
     * Validate if the provided class is a valid service class.
     *
     * @param string $class
     *
     * @return void
     * @throws TypeError
     */
    private function validateServiceClass(string $class): void
    {
        if (!class_exists($class)) {
            throw new TypeError("{$class} not found");
        }

        $instance = new $class();

        if (!($instance instanceof ServiceInterface)) {
            throw new TypeError("{$class} is not ServiceInterface");
        }
    }

    /**
     * Handle a GRPC exception.
     *
     * @param GRPCException $e
     * @param Context       $context
     *
     * @return void
     */
    private function handleGRPCException(GRPCException $e, Context $context): void
    {
        Util::log(SWOOLE_LOG_ERROR, $e->getMessage() . ', error code: ' . $e->getCode() . "\n" . $e->getTraceAsString());
        $context = $context->withValue(Constant::GRPC_STATUS, $e->getCode());
        $context = $context->withValue(Constant::GRPC_MESSAGE, $e->getMessage());
    }
}
