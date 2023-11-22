<?php

declare(strict_types=1);
namespace Minichan\Middleware;

use Minichan\Grpc\MessageInterface;
use Minichan\Grpc\RequestHandlerInterface;

/**
 * 
 *
 * Represents a stack of middlewares to handle GRPC requests.
 */
class StackHandler implements RequestHandlerInterface
{
    /** @var array List of middlewares in the stack. */
    private array $middlewares = [];

    /**
     * StackHandler constructor.
     *
     * @param MiddlewareInterface ...$middlewares
     */
    public function __construct(MiddlewareInterface ...$middlewares)
    {
        $this->middlewares = $middlewares;
    }

    /**
     * Add a middleware to the stack.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return StackHandler
     */
    public function add(MiddlewareInterface $middleware): self
    {
        $stack = clone $this;
        array_unshift($stack->middlewares, $middleware);
        return $stack;
    }

    /**
     * Handle the GRPC request using the middleware stack.
     *
     * @param MessageInterface $request
     *
     * @return MessageInterface|null
     */
    public function handle(MessageInterface $request): ?MessageInterface
    {
        $middleware = $this->middlewares[0] ?? null;
        return $middleware
            ? $middleware->process($request, $this->next($middleware))
            : null;
    }

    /**
     * Get the next middleware in the stack.
     *
     * @param MiddlewareInterface $middleware
     *
     * @return StackHandler
     */
    private function next(MiddlewareInterface $middleware): self
    {
        $stack = clone $this;
        array_shift($stack->middlewares);
        return $stack;
    }
}

