<?php

declare(strict_types=1);
namespace Minichan\Grpc;

/**
 * 
 * Represents the contract for a context containing key-value pairs.
 */
interface ContextInterface
{
    /**
     * Create a new context with the specified key and value.
     *
     * @param string $key   The key for the new context entry.
     * @param mixed  $value The value for the new context entry.
     *
     * @return ContextInterface The new context with the added key-value pair.
     */
    public function withValue(string $key, $value): self;

    /**
     * Get the value associated with the specified key from the context.
     *
     * @param string $key The key to retrieve the value for.
     *
     * @return mixed The value associated with the key.
     */
    public function getValue(string $key);

    /**
     * Get all values stored in the context.
     *
     * @return array The array of key-value pairs in the context.
     */
    public function getValues(): array;
}
