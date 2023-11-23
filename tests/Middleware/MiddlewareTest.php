<?php

declare(strict_types=1);
namespace Tests\Middleware;

use Minichan\Grpc\MessageInterface;
use Minichan\Middleware\MiddlewareInterface;
use Minichan\Middleware\StackHandler;

class MiddlewareTest extends \PHPUnit\Framework\TestCase
{
    /**
    * @covers ::testHandleWithoutMiddleware
    */
    public function testHandleWithoutMiddleware()
    {
        $stackHandler = new StackHandler();

        $request = $this->createMock(MessageInterface::class);

        $result = $stackHandler->handle($request);

        $this->assertNull($result);
    }

    /**
    * @covers ::testHandleWithMiddleware
    */
    public function testHandleWithMiddleware()
    {
        $mockMiddleware = $this->createMock(MiddlewareInterface::class);
        $mockMiddleware
            ->expects($this->once())
            ->method('process')
            ->willReturnCallback(function (MessageInterface $request, StackHandler $stack) {
                return $request;
            });

        $stackHandler = new StackHandler($mockMiddleware);

        $mockRequest = $this->createMock(MessageInterface::class);

        $result = $stackHandler->handle($mockRequest);

        $this->assertInstanceOf(MessageInterface::class, $result);
    }
}