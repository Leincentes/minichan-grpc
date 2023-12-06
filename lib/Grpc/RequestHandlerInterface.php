<?php

declare(strict_types=1);

namespace Minichan\Grpc;

/**
 * 
 *
 * This interface defines the contract for classes that will handle gRPC requests.
 *
 * Classes implementing this interface are expected to provide a method for processing
 * gRPC requests and returning an appropriate response.
 */
interface RequestHandlerInterface
{
    /**
     * Process the gRPC request and return a gRPC response.
     *
     * @param MessageInterface $request The gRPC request object.
     *
     * @return MessageInterface The gRPC response object.
     */
    public function handle(MessageInterface $request): ?MessageInterface;
}
