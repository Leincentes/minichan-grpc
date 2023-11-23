<?php

declare(strict_types=1);

use Minichan\Grpc\Server;
use Minichan\Grpc\ServiceContainer;
use Minichan\Middleware\LoggingMiddleware;
use Minichan\Middleware\StackHandler;
use PHPUnit\Framework\TestCase;
use Services\AuthService;

class ServerTest extends TestCase
{
   /**
     * @covers ::testServerInitialization
     */
    public function testServerInitialization()
    {
        $server = new Server('127.0.0.1', 9501);
        $this->assertInstanceOf(Server::class, $server);
    }

    /**
    * @covers ::testServerInitialization
    */
    public function testRegisterServices()
    {
        $server = new Server('127.0.0.1', 9501, SWOOLE_BASE, SWOOLE_SOCK_TCP);

        $server->registerServices([AuthService::class]);

        $registeredServices = $this->getPrivateProperty($server, 'services');

        $this->assertCount(1, $registeredServices);
    }

    /**
     * @covers ::testWithWorkerContext
     */
    public function testWithWorkerContext()
    {
        $server = new Server('127.0.0.1', 9501);

        $workerContextCallback = function () {
            return 'worker_context_value';
        };

        $server->withWorkerContext('worker_key', $workerContextCallback);

        $workerContexts = $this->getPrivateProperty($server, 'workerContexts');

        $this->assertArrayHasKey('worker_key', $workerContexts);
        $this->assertEquals($workerContextCallback, $workerContexts['worker_key']);
    }

    /**
     * @covers ::testAddMiddleware
     */
    public function testAddMiddleware()
    {
        $server = new Server('127.0.0.1', 9501);

        $middleware = new LoggingMiddleware();

        $server->addMiddleware($middleware);

        $handler = $this->getPrivateProperty($server, 'handler');

        $this->assertInstanceOf(StackHandler::class, $handler);
    }

    /**
     * Helper method to get the value of a private or protected property.
     *
     * @param object $object
     * @param string $propertyName
     * @return mixed
     * @throws ReflectionException
     */
    private function getPrivateProperty($object, $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}
