<?php

use Minichan\Config\Config;
use Minichan\Middleware\LoggingMiddleware;
use Minichan\Middleware\TraceMiddleware;
use PHPUnit\Framework\TestCase;
use Services\AuthService;

class ConfigTest extends TestCase
{
    /**
    * @covers ::getServices
    */
    public function testGetServices()
    {
        $mockedServices = [AuthService::class];
        $configMock = $this->getMockBuilder(\Services\Config\Config::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configMock->method('registerServices')->willReturn($mockedServices);

        $this->assertEquals($mockedServices, Config::getServices());
    }

    /**
    * @covers Config::getMiddlewares
    */
    public function testGetMiddlewares()
    {
        $loggingMiddleware = new LoggingMiddleware();
        $traceMiddleware = new TraceMiddleware();

        $this->assertEquals([$loggingMiddleware, $traceMiddleware], Config::getMiddlewares());
    }
}
