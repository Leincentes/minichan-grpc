<?php

declare(strict_types=1);
namespace Minichan\Middleware;

/**
 * 
 *
 * Represents the middleware interface for GRPC request processing.
 */
interface MiddlewareInterface
{
    /**
     * Process the GRPC request using the middleware.
     *
     * @param \Minichan\Grpc\Request $request
     * @param \Minichan\Middleware\StackHandler $handler
     *
     * @return \Minichan\Grpc\MessageInterface
     */
    public function process(\Minichan\Grpc\Request | \Minichan\Grpc\MessageInterface $request, \Minichan\Middleware\StackHandler $handler): \Minichan\Grpc\MessageInterface;
}
