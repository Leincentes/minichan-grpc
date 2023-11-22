<?php

declare(strict_types=1);
namespace Minichan\Grpc;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use ReturnTypeWillChange;
use Traversable;

final class Context implements ContextInterface, IteratorAggregate, Countable, ArrayAccess
{
    /** @var array The storage for context values. */
    private $values;

    /**
     * Context constructor.
     *
     * @param array $values The initial values for the context.
     */
    public function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * Create a new context with the specified key and value.
     *
     * @param string $key   The key for the new context entry.
     * @param mixed  $value The value for the new context entry.
     *
     * @return ContextInterface The new context with the added key-value pair.
     */
    public function withValue(string $key, $value): ContextInterface
    {
        $context = clone $this;
        $context->values[$key] = $value;
        return $context;
    }

    /**
     * Get the value associated with the specified key from the context.
     *
     * @param string $key     The key to retrieve the value for.
     * @param mixed  $default The default value if the key is not found.
     *
     * @return mixed The value associated with the key or the default value.
     */
    public function getValue(string $key, $default = null)
    {
        return $this->values[$key] ?? $default;
    }

    /**
     * Get all values stored in the context.
     *
     * @return array The array of key-value pairs in the context.
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Check if the specified offset (key) exists in the context.
     *
     * @param mixed $offset The offset (key) to check.
     *
     * @return bool True if the offset exists, false otherwise.
     */
    #[ReturnTypeWillChange]
    public function offsetExists($offset): bool
    {
        assert(is_string($offset), 'Offset argument must be a type of string');
        return isset($this->values[$offset]) || array_key_exists($offset, $this->values);
    }

    /**
     * Get the value associated with the specified offset (key).
     *
     * @param mixed $offset The offset (key) to retrieve the value for.
     *
     * @return mixed The value associated with the offset or null if not found.
     */
    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        assert(is_string($offset), 'Offset argument must be a type of string');
        return $this->values[$offset] ?? null;
    }

    /**
     * Set the value for the specified offset (key).
     *
     * @param mixed $offset The offset (key) to set the value for.
     * @param mixed $value  The value to set for the offset.
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value): void
    {
        assert(is_string($offset), 'Offset argument must be a type of string');
        $this->values[$offset] = $value;
    }

    /**
     * Unset the specified offset (key) in the context.
     *
     * @param mixed $offset The offset (key) to unset.
     *
     * @return void
     */
    #[ReturnTypeWillChange]
    public function offsetUnset($offset): void
    {
        assert(is_string($offset), 'Offset argument must be a type of string');
        unset($this->values[$offset]);
    }

    /**
     * Get an iterator for iterating over the values in the context.
     *
     * @return Traversable The iterator for the context values.
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->values);
    }

    /**
     * Get the number of values in the context.
     *
     * @return int The count of values in the context.
     */
    public function count(): int
    {
        return count($this->values);
    }
}
