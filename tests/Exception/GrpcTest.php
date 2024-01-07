<?php

declare(strict_types=1);

namespace Tests\Exception;

use Minichan\Config\Status;
use Minichan\Exception\GRPCException;

class GrpcTest extends \PHPUnit\Framework\TestCase
{
    /**
    * @covers ::createGRPCException
    */
    public function testCreateGRPCException()
    {
        // Arrange
        $errorMessage = 'An error occurred during GRPC operation';
        $errorCode = 123; 
        $previousException = new \Exception('Previous exception for testing');

        // Act
        $grpcException = GRPCException::create($errorMessage, $errorCode, $previousException);

        // Assert
        $this->assertInstanceOf(GRPCException::class, $grpcException);
        $this->assertSame($errorMessage, $grpcException->getMessage());
        $this->assertSame($errorCode, $grpcException->getCode());
        $this->assertSame($previousException, $grpcException->getPrevious());
    }

    /**
    * @covers ::defaultErrorCode
    */
    public function testDefaultErrorCode()
    {
        // Arrange
        $errorMessage = 'An error occurred during GRPC operation';

        // Act
        $grpcException = GRPCException::create($errorMessage);

        // Assert
        $this->assertInstanceOf(GRPCException::class, $grpcException);
        // Check that the default error code is used when not provided
        $this->assertSame(Status::UNKNOWN, $grpcException->getCode());
    }
}
