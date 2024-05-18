<?php

declare(strict_types=1);
namespace Minichan\Grpc;

/**
 * 
 * Represents a gRPC request with an associated context, service, method, and payload.
 */
final class Request implements MessageInterface
{
    /** @var Context The context associated with the request. */
    private Context $context;

    /** @var string The gRPC service name. */
    private string $service;

    /** @var string The gRPC method name. */
    private string $method;

    /** @var string The payload of the request. */
    private string $payload;

    /** @var mixed The actual message content. */
    private $message;

    /**
     * Request constructor.
     *
     * @param Context $context The context associated with the request.
     * @param string  $service The gRPC service name.
     * @param string  $method  The gRPC method name.
     * @param string  $payload The payload of the request.
     */
    public function __construct(Context $context, string $service, string $method, string $payload)
    {
        $this->context = $context;
        $this->service = $service;
        $this->method = $method;
        $this->payload = $payload;
    }

    /**
     * Get the gRPC service name.
     *
     * @return string The gRPC service name.
     */
    public function getService(): string
    {
        return $this->service;
    }

    /**
     * Get the gRPC method name.
     *
     * @return string The gRPC method name.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Get the payload of the request.
     *
     * @return string The payload of the request.
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * Get the context associated with the request.
     *
     * @return Context The context associated with the request.
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * Set a new context for the request.
     *
     * @param Context $context The new context for the request.
     *
     * @return Request The request with the updated context.
     */
    public function withContext(Context $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Get the actual message content.
     *
     * @return mixed The message content.
     */
    public function getMessage()
    {
        return $this->message;
    }
    
    public function getHeaders(): array
    {
        return $this->context->getValue(\Swoole\Http\Request::class)->header ?? [];
    }

    public function getParams(): array
    {
        return $this->context->getValue(\Swoole\Http\Request::class)->get ?? [];
    }
    public function getDeadline(): ?int
    {
        $headers = $this->getHeaders();
        return isset($headers['x-deadline']) ? (int) $headers['x-deadline'] : null;
    }

    public function isDeadlineExceeded(): bool
    {
        $deadline = $this->getDeadline();
        return $deadline !== null && time() > $deadline;
    }
    public function getParam(string $key): ?string
    {
        return $this->getParams()[$key] ?? null;
    }

    public function validateParam(string $key): void
    {
        if (empty($this->getParam($key))) {
            throw new \Minichan\Exception\InvalidArgumentException("Invalid argument: $key");
        }
    }

}
