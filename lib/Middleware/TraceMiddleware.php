<?php

declare(strict_types=1);
namespace Minichan\Middleware;

/**
 * 
 *
 * A middleware for tracing GRPC requests. This middleware does not modify the request and simply passes it to the next handler in the stack.
 */
class TraceMiddleware implements MiddlewareInterface
{
    /**
     * Process the GRPC request.
     *
     * @param \Minichan\Grpc\MessageInterface $request
     * @param StackHandler $handler
     *
     * @return \Minichan\Grpc\MessageInterface
     */
    public function process(\Minichan\Grpc\MessageInterface $request, \Minichan\Middleware\StackHandler $handler): \Minichan\Grpc\MessageInterface
    {
        // This middleware does not modify the request; it simply passes it to the next handler in the stack.
        return $handler->handle($request);
    }
}
