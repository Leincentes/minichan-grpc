<?php

declare(strict_types=1);
namespace Minichan\Grpc;

/**
 * 
 * Represents a message with an associated context.
 */
final class Message implements MessageInterface
{
    /** @var Context The context associated with the message. */
    private Context $context;

    /** @var mixed The actual message content. */
    private $message;

    /**
     * Message constructor.
     *
     * @param Context $context The context associated with the message.
     * @param mixed   $message The actual message content.
     */
    public function __construct(Context $context, $message)
    {
        $this->context = $context;
        $this->message = $message;
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

    /**
     * Get the context associated with the message.
     *
     * @return Context The context associated with the message.
     */
    public function getContext()
    {
        return $this->context;
    }
}
