<?php

declare(strict_types=1);
namespace Minichan\Grpc;

/**
 * 
 * Represents a gRPC response with an associated context and payload.
 */
final class Response implements MessageInterface
{
    /** @var Context The context associated with the response. */
    private Context $context;

    /** @var string The payload of the response. */
    private string $payload;

    /** @var mixed The actual message content. */
    private $message;

    /**
     * Response constructor.
     *
     * @param Context $context The context associated with the response.
     * @param string  $payload The payload of the response.
     */
    public function __construct(Context $context, string $payload)
    {
        $this->context = $context;
        $this->payload = $payload;
    }

    /**
     * Get the payload of the response.
     *
     * @return string The payload of the response.
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * Get the context associated with the response.
     *
     * @return Context The context associated with the response.
     */
    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * Get the actual message content.
     *
     * @return mixed The message content.
     */
    public function getMessage()
    {
        // Note: You might want to implement this method if $message is meant to be used.
        return $this->message;
    }
}
