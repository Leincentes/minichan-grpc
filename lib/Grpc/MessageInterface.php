<?php

declare(strict_types=1);
namespace Minichan\Grpc;

/**
 * 
 * Represents the contract for a message.
 */
interface MessageInterface
{
    /**
     * Get the actual message content.
     *
     * @return mixed The message content.
     */
    public function getMessage();

    /**
     * Get the context associated with the message.
     *
     * @return Context The context associated with the message.
     */
    public function getContext();
}