<?php

declare(strict_types=1);
namespace Minichan\Grpc;

/**
 * 
 * Represents the contract for a gRPC client.
 */
interface ClientInterface
{
    /**
     * Set additional settings for the client.
     *
     * @param array $settings The additional settings to be set.
     *
     * @return self The current instance for method chaining.
     */
    public function set(array $settings): self;

    /**
     * Establish a connection to the remote gRPC server.
     *
     * @return self The current instance for method chaining.
     */
    public function connect(): self;

    /**
     * Get statistics about the client's connection.
     *
     * @return array The statistics about the client's connection.
     */
    public function stats(): array;

    /**
     * Close the connection to the remote gRPC server.
     *
     * @return void
     */
    public function close();

    /**
     * Send a message to the remote gRPC server.
     *
     * @param mixed $method The gRPC method to be called.
     * @param mixed $message The message to be sent.
     * @param string $type The type of the message (default is 'proto').
     * @param string $user_agent The agent request.
     *
     * @return mixed The stream ID of the sent message.
     */
    public function send($method, $message, $type = 'proto', $user_agent = 'minichan/v1');

    /**
     * Receive data from a specific stream in the established connection.
     *
     * @param mixed $streamId The ID of the stream from which to receive data.
     * @param mixed $timeout The timeout for the receive operation (default is -1, indicating no timeout).
     *
     * @return mixed The received data.
     */
    public function recv($streamId, $timeout = -1);

    /**
     * Push a message to the remote gRPC server (used in client-side streaming mode).
     *
     * @param mixed $streamId The ID of the stream to which the message should be pushed.
     * @param mixed $message The message to be pushed.
     * @param string $type The type of the message (default is 'proto').
     * @param bool $end Whether to end the stream after pushing the message (default is false).
     *
     * @return bool The success status of the push operation.
     */
    public function push($streamId, $message, $type = 'proto', $end = false);
}
